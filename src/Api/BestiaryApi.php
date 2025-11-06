<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Api;

use Velkuns\GameTextEngine\Api\Exception\BestiaryException;
use Velkuns\GameTextEngine\Element\Damage\Damages;
use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Factory\EntityFactory;

/**
 * @phpstan-import-type EntityData from EntityInterface
 * @phpstan-import-type DamagesData from Damages
 * @phpstan-type BestiaryData array{
 *    name: string,
 *    type: string,
 *    race: string,
 *    gender?: string,
 *    size: string,
 *    abilities: array<string, int>,
 *    inventory?: array{coins?: int, items?: list<string>},
 *    damages?: DamagesData|null,
 * }
 */
class BestiaryApi
{
    /** @var array<string, EntityInterface> $bestiary */
    private array $bestiary = [];

    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly ItemsApi $items,
    ) {}

    /**
     * @phpstan-param list<BestiaryData> $list
     */
    public function load(array $list): void
    {
        $this->bestiary = [];
        foreach ($list as $data) {
            $fullData = $this->fromBestiary($data);
            $entity   = $this->entityFactory->from($fullData);
            $this->bestiary[\strtolower($entity->getName())] = $entity;
        }
    }

    public function get(string $name, bool $asClone = true): EntityInterface
    {
        $lowerCaseName = \strtolower($name);
        if (!isset($this->bestiary[$lowerCaseName])) {
            throw new BestiaryException("Entity '$name' not found in bestiary.", 1701);
        }

        return $asClone ? clone $this->bestiary[$lowerCaseName] : $this->bestiary[$lowerCaseName];
    }

    public function set(EntityInterface $entity): self
    {
        $this->bestiary[\strtolower($entity->getName())] = $entity;

        return $this;
    }

    public function remove(string $name): self
    {
        $lowerCaseName = \strtolower($name);
        if (isset($this->bestiary[$lowerCaseName])) {
            unset($this->bestiary[$lowerCaseName]);
        }

        return $this;
    }

    public function dump(bool $prettyPrint = false): string
    {
        /** @var list<BestiaryData> $data */
        $data = [];

        try {
            foreach ($this->bestiary as $entity) {
                $bestiaryData = [
                    'name'      => $entity->getName(),
                    'type'      => $entity->getType(),
                    'race'      => $entity->getInfo()->race,
                    'gender'    => $entity->getInfo()->gender,
                    'size'      => $entity->getInfo()->size,
                    'damages'   => $entity->getDamages()->jsonSerialize(),
                    'abilities' => [
                        'strength'  => $entity->getAbilities()->get('strength')?->getValue() ?? 0,
                        'endurance' => $entity->getAbilities()->get('endurance')?->getValue() ?? 0,
                        'agility'   => $entity->getAbilities()->get('agility')?->getValue() ?? 0,
                        'intuition' => $entity->getAbilities()->get('intuition')?->getValue() ?? 0,
                    ],
                ];

                $inventory = [];
                foreach ($entity->getInventory()->items as $item) {
                    $inventory[] = $item->getName();
                }

                if ($inventory !== [] || $entity->getInventory()->coins > 0) {
                    $bestiaryData['inventory'] = [
                        'coins' => $entity->getInventory()->coins,
                        'items' => $inventory,
                    ];
                }

                $data[] = $bestiaryData;
            }

            return \json_encode($data, flags: \JSON_THROW_ON_ERROR | ($prettyPrint ? \JSON_PRETTY_PRINT : 0));
            // @codeCoverageIgnoreStart
        } catch (\JsonException $exception) {
            throw new BestiaryException('Unable to dump bestiary data: ' . $exception->getMessage(), 1700, $exception);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @phpstan-param BestiaryData $data
     * @phpstan-return EntityData
     */
    private function fromBestiary(array $data): array
    {
        $info = [
            'level'       => 1,
            'xp'          => 0,
            'age'         => 0,
            'race'        => $data['race'],
            'gender'      => $data['gender'] ?? 'unknown',
            'size'        => $data['size'],
            'description' => '',
            'background'  => '',
            'notes'       => '',
        ];

        $damages = $data['damages'] ?? [];

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
        $abilities['bases']['vitality'] = [
            'type'        => 'base',
            'name'        => 'vitality',
            'initial'     => 0,
            'max'         => 0,
            'value'       => 0,
            'constraints' => ['min' => 0, 'max' => 40],
            'rule'        => 'strength + endurance',
        ];
        $abilities['compounds']['attack']  = ['type' => 'compound', 'name' => 'attack', 'rule' => 'strength + agility'];
        $abilities['compounds']['defense'] = ['type' => 'compound', 'name' => 'defense','rule' => 'endurance + intuition'];

        //~ Initialize empty statuses
        $statuses  = ['skills' => [], 'states' => [], 'blessings' => [], 'curses' => [], 'titles' => []];

        //~ Build inventory
        $inventory = [
            'coins' => $data['inventory']['coins'] ?? 0,
            'items' => [],
        ];
        foreach ($data['inventory']['items'] ?? [] as $itemName) {
            $inventory['items'][] = $this->items->get($itemName)->jsonSerialize();
        }

        //~ Return full data for factory
        return [
            'name'      => $data['name'],
            'type'      => $data['type'],
            'info'      => $info,
            'damages'   => $damages,
            'abilities' => $abilities,
            'statuses'  => $statuses,
            'inventory' => $inventory,
        ];
    }
}
