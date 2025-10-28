<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Api;

use Velkuns\GameTextEngine\Api\Exception\PlayerException;
use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Factory\EntityFactory;
use Velkuns\GameTextEngine\Element\Modifier\ModifierProcessor;

/**
 * @phpstan-import-type EntityData from EntityInterface
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

    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly ItemsApi $items,
        private readonly AbilitiesApi $abilitiesApi,
        private readonly StatusesApi $statusesApi,
        private readonly ModifierProcessor $modifierProcessor,
    ) {}

    /**
     * @phpstan-param EntityData $data
     */
    public function load(array $data): void
    {
        $this->player = $this->entityFactory->from($data);
    }

    public function consume(string $itemName, ?EntityInterface $enemy = null): self
    {
        $item = $this->player->getInventory()->get($itemName);
        if ($item === null) {
            throw new PlayerException('Cannot consume item "' . $itemName . '": item not found in inventory.', 1410);
        }

        if (!$item->isConsumable()) {
            throw new PlayerException('Cannot consume item "' . $itemName . '": item is not consumable.', 1411);
        }

        foreach ($item->getModifiers() as $modifier) {
            $this->modifierProcessor->apply($modifier, $this->player, $enemy);
        }

        $this->player->getInventory()->consume($itemName);

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
            return \json_encode($this->player, flags: \JSON_THROW_ON_ERROR | ($prettyPrint ? \JSON_PRETTY_PRINT : 0));
            // @codeCoverageIgnoreStart
        } catch (\JsonException) {
            throw new PlayerException('Unable to dump player to JSON.', 1401); // @codeCoverageIgnore
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
            'level'       => 1,
            'xp'          => 0,
            'age'         => $data['age'] ?? 20,
            'race'        => $data['race'] ?? 'human',
            'size'        => 'medium',
            'gender'      => $data['gender'] ?? 'unknown',
            'description' => $data['description'] ?? '',
            'background'  => $data['background'] ?? '',
            'notes'       => '',
        ];

        //~ Build abilities
        $abilities = $this->abilitiesApi->fromNewPlayer($data['abilities']);

        //~ Initialize empty statuses
        $statuses = $this->statusesApi->fromNewPlayer($data['statuses'] ?? []);

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
