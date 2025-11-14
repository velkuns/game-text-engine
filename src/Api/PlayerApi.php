<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Api;

use Velkuns\GameTextEngine\Core\Evaluator\Evaluator;
use Velkuns\GameTextEngine\Core\Factory\EntityFactory;
use Velkuns\GameTextEngine\Exception\Api\PlayerApiException;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;
use Velkuns\GameTextEngine\Rpg\Modifier\ModifierHandler;
use Velkuns\GameTextEngine\Rpg\Trait\TraitInterface;
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
 *    attributes: array<string, int>,
 *    traits?: array<string, list<string>>,
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
        private readonly AttributesApi $attributes,
        private readonly TraitsApi $traits,
        private readonly ModifierHandler $modifierHandler,
        private readonly Evaluator $evaluator,
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
     * @param array<string, int> $attributes
     * @param array<string, list<string>> $traits
     */
    public function levelUp(array $attributes, array $traits = []): self
    {
        //~ Check player info
        $this->rules->leveling->assertMaxLevelNotReached($this->player->getInfo()->level);
        $this->rules->leveling->assertHasEnoughXp($this->evaluator, $this->player);

        //~ Check attributes
        $this->attributes->rules->leveling->assertHasCorrectAttribution($attributes);

        //~ Check traits
        $this->traits->rules->leveling->assertCanAttributeOnNextLevel($this->player->getInfo()->level, $traits);
        $this->traits->rules->leveling->assertHasCorrectAttribution($traits);
        $this->traits->rules->assertAllTraitsExist($traits);

        //~ All is ok, then update attributes
        foreach ($attributes as $name => $value) {
            $this->player->getAttributes()->get($name)?->increase($value);
        }

        //~ Add traits
        foreach ($traits as $type => $list) {
            foreach ($list as $name) {
                /** @var TraitInterface $trait */
                $trait = $this->traits->get($type, $name);
                $this->player->getTraits()->set($trait);
            }
        }

        //~ Decrease xp
        $this->player->getInfo()->xp -= $this->rules->leveling->getXpStep($this->evaluator, $this->player);

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

        //~ Build attributes
        $attributes = $this->attributes->fromNewPlayer($data['attributes']);

        //~ Initialize empty traits
        $traits = $this->traits->fromNewPlayer($data['traits'] ?? []);

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
            'attributes' => $attributes,
            'traits'  => $traits,
            'inventory' => $inventory,
        ];
    }
}
