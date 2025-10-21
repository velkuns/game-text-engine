<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Velkuns\GameTextEngine\Api;

use Velkuns\GameTextEngine\Api\Exception\StoryException;
use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Factory\EntityFactory;

/**
 * @phpstan-import-type EntityData from EntityInterface
 * @phpstan-type NewPlayerData array{
 *    name: string,
 *    age?: int,
 *    race?: string,
 *    description?: string,
 *    background?: string,
 *    abilities: array<string, int>,
 *    inventory?: list<string>,
 * }
 */
class Player
{
    public EntityInterface $player;

    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly Items $items,
    ) {}

    /**
     * @phpstan-param EntityData $data
     */
    public function load(array $data): void
    {
        $this->player = $this->entityFactory->from($data);
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
            throw new StoryException('Unable to dump player to JSON.', 1401); // @codeCoverageIgnore
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @phpstan-param NewPlayerData $data
     * @phpstan-return EntityData
     */
    private function fromNewData(array $data): array
    {
        $coins = 10;
        $info  = [
            'level'       => 1,
            'xp'          => 0,
            'damages'     => 0,
            'age'         => $data['age'] ?? 20,
            'race'        => $data['race'] ?? 'human',
            'size'        => 'medium',
            'description' => $data['description'] ?? '',
            'background'  => $data['background'] ?? '',
            'notes'       => '',
        ];

        //~ Build abilities
        $abilities = ['bases' => [], 'compounds' => []];
        foreach ($data['abilities'] as $name => $value) {
            $abilities['bases'][$name] = [
                'type'        => 'base',
                'name'        => $name,
                'initial'     => $value,
                'max'         => $value,
                'value'       => $value,
                'constraints' => ['min' => 0, 'max' => 20],
                'rule'        => null,
            ];
        }
        $abilities['bases']['vitality']    = [
            'type'        => 'base',
            'name'        => 'vitality',
            'initial'     => 0,
            'max'         => 0,
            'value'       => 0,
            'constraints' => ['min' => 0, 'max' => 40],
            'rule'        => 'strength + endurance',
        ];
        $abilities['compounds']['attack']  = ['type' => 'compound', 'name' => 'attack', 'rule' => 'strength + agility'];
        $abilities['compounds']['defense'] = ['type' => 'compound', 'name' => 'defense', 'rule' => 'endurance + intuition'];

        //~ Initialize empty statuses
        $statuses = [
            'skills'    => [],
            'states'    => [],
            'blessings' => [],
            'curses'    => [],
            'titles'    => [],
        ];

        //~ Build inventory
        $inventory = [];
        foreach ($data['inventory'] ?? [] as $itemName) {
            $inventory[] = $this->items->get($itemName)->jsonSerialize();
        }

        //~ Return full data for factory
        return [
            'name'      => $data['name'],
            'type'      => 'player',
            'coins'     => $coins,
            'info'      => $info,
            'abilities' => $abilities,
            'statuses'  => $statuses,
            'inventory' => $inventory,
        ];
    }
}
