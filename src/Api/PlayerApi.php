<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Api;

use Velkuns\GameTextEngine\Core\Factory\EntityFactory;
use Velkuns\GameTextEngine\Exception\Api\PlayerApiException;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;
use Velkuns\GameTextEngine\Rpg\Modifier\ModifierHandler;
use Velkuns\GameTextEngine\Rpg\Status\StatusInterface;
use Velkuns\GameTextEngine\Rules\Player\PlayerRules;
use Velkuns\GameTextEngine\Rules\Player\PlayerRulesInventory;
use Velkuns\GameTextEngine\Rules\Player\PlayerRulesLeveling;
use Velkuns\GameTextEngine\Rules\Player\PlayerRulesStarting;

/**
 * @phpstan-import-type EntityData from EntityInterface
 * @phpstan-import-type PlayerRulesData from PlayerRules
 * @phpstan-type NewPlayerData array{
 *    name: string,
 *    age?: int,
 *    race?: string,
 *    gender?: string,
 *    description?: string,
 *    background?: string,
 *    abilities: array<string, int>,
 *    statuses?: array<string, list<string>>,
 *    inventory?: list<string>,
 * }
 */
class PlayerApi
{
    public EntityInterface $player;
    public PlayerRules $rules;

    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly ItemsApi $items,
        private readonly AbilitiesApi $abilities,
        private readonly StatusesApi $statuses,
        private readonly ModifierHandler $modifierHandler,
    ) {}

    /**
     * @phpstan-param PlayerRulesData $playerRulesData
     * @phpstan-param ?EntityData $playerData
     */
    public function load(array $playerRulesData, ?array $playerData): void
    {
        $description = $playerRulesData['description'];
        $starting    = new PlayerRulesStarting(...$playerRulesData['starting']);
        $leveling    = new PlayerRulesLeveling(...$playerRulesData['leveling']);
        $inventory   = new PlayerRulesInventory(...$playerRulesData['inventory']);

        $this->rules  = new PlayerRules($description, $starting, $leveling, $inventory);

        if ($playerData !== null) {
            $this->player = $this->entityFactory->from($playerData);
        }
    }

    public function consume(string $itemName, ?EntityInterface $enemy = null): self
    {
        $item = $this->player->getInventory()->get($itemName);
        if ($item === null) {
            throw new PlayerApiException('Cannot consume item "' . $itemName . '": item not found in inventory.', 1410);
        }

        if (!$item->isConsumable()) {
            throw new PlayerApiException('Cannot consume item "' . $itemName . '": item is not consumable.', 1411);
        }

        foreach ($item->getModifiers() as $modifier) {
            $this->modifierHandler->handle($modifier, $this->player, $enemy);
        }

        $this->player->getInventory()->consume($itemName);

        return $this;
    }

    /**
     * @param array<string, int> $abilities
     * @param array<string, list<string>> $statuses
     */
    public function levelUp(array $abilities, array $statuses = []): self
    {
        //~ Check player info
        $this->rules->leveling->assertMaxLevelNotReached($this->player->getInfo()->level);
        $this->rules->leveling->assertHasEnoughXp($this->player->getInfo()->level, $this->player->getInfo()->xp);

        //~ Check abilities
        $this->abilities->rules->leveling->assertHasCorrectAttribution($abilities);

        //~ Check statuses
        $this->statuses->rules->leveling->assertCanAttributeOnNextLevel($this->player->getInfo()->level, $statuses);
        $this->statuses->rules->leveling->assertHasCorrectAttribution($statuses);
        $this->statuses->rules->assertAllStatusesExist($statuses);

        //~ All is ok, then update abilities
        foreach ($abilities as $name => $value) {
            $this->player->getAbilities()->get($name)?->increase($value);
        }

        //~ Add statuses
        foreach ($statuses as $type => $list) {
            foreach ($list as $name) {
                /** @var StatusInterface $status */
                $status = $this->statuses->get($type, $name);
                $this->player->getStatuses()->set($status);
            }
        }

        //~ Decrease xp
        $this->player->getInfo()->xp -= $this->rules->leveling->getXpStep($this->player->getInfo()->level);

        //~ Increase level
        $this->player->getInfo()->level += 1;

        return $this;
    }

    /**
     * @phpstan-param NewPlayerData $data
     */
    public function new(array $data): self
    {
        $entityData   = $this->fromNewData($data);
        $this->player = $this->entityFactory->from($entityData);

        return $this;
    }

    public function dump(bool $prettyPrint = false): string
    {
        try {
            return \json_encode($this->rules, flags: \JSON_THROW_ON_ERROR | ($prettyPrint ? \JSON_PRETTY_PRINT : 0));
            // @codeCoverageIgnoreStart
        } catch (\JsonException) {
            throw new PlayerApiException('Unable to dump player rules to JSON.', 1401); // @codeCoverageIgnore
        }
        // @codeCoverageIgnoreEnd
    }

    public function dumpPlayer(bool $prettyPrint = false): string
    {
        try {
            return \json_encode($this->player, flags: \JSON_THROW_ON_ERROR | ($prettyPrint ? \JSON_PRETTY_PRINT : 0));
            // @codeCoverageIgnoreStart
        } catch (\JsonException) {
            throw new PlayerApiException('Unable to dump player data to JSON.', 1401); // @codeCoverageIgnore
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @phpstan-param NewPlayerData $data
     * @phpstan-return EntityData
     */
    private function fromNewData(array $data): array
    {
        $info  = [
            'level'       => $this->rules->starting->level,
            'xp'          => $this->rules->starting->xp,
            'age'         => $data['age'] ?? 20,
            'race'        => $data['race'] ?? 'human',
            'size'        => 'medium',
            'gender'      => $data['gender'] ?? 'unknown',
            'description' => $data['description'] ?? '',
            'background'  => $data['background'] ?? '',
            'notes'       => '',
        ];

        //~ Build abilities
        $abilities = $this->abilities->fromNewPlayer($data['abilities']);

        //~ Initialize empty statuses
        $statuses = $this->statuses->fromNewPlayer($data['statuses'] ?? []);

        //~ Build inventory
        $inventory = ['coins' => 10, 'items' => []];
        foreach ($data['inventory'] ?? [] as $itemName) {
            $inventory['items'][] = $this->items->get($itemName)->jsonSerialize();
        }

        //~ Return full data for factory
        return [
            'name'      => $data['name'],
            'type'      => 'player',
            'info'      => $info,
            'damages'   => ['physical' => ['type' => 'physical', 'value' => 0]],
            'abilities' => $abilities,
            'statuses'  => $statuses,
            'inventory' => $inventory,
        ];
    }
}
