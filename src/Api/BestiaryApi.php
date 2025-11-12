<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Api;

use Random\Randomizer;
use Velkuns\GameTextEngine\Core\Factory\EntityFactory;
use Velkuns\GameTextEngine\Exception\Api\BestiaryApiException;
use Velkuns\GameTextEngine\Rpg\Damages\Damages;
use Velkuns\GameTextEngine\Rpg\Entity\EntityEquipment;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;
use Velkuns\GameTextEngine\Rpg\Entity\EntityLoot;

/**
 * @phpstan-import-type EntityData from EntityInterface
 * @phpstan-import-type DamagesData from Damages
 * @phpstan-import-type EntityLootData from EntityLoot
 * @phpstan-import-type EquipmentData from EntityEquipment
 * @phpstan-type BestiaryData array{
 *    name: string,
 *    type: string,
 *    race: string,
 *    size: string,
 *    level?: int,
 *    gender?: string,
 *    attributes: array<string, int>,
 *    inventory?: array{coins?: int, items?: list<string>},
 *    loot?: EntityLootData,
 *    equipment?: EquipmentData,
 *    damages?: DamagesData|null,
 * }
 * @phpstan-type BestiaryFileData array{
 *    autoLeveling: array{
 *        attributes: array<string, int>,
 *    },
 *    list: list<BestiaryData>,
 * }
 */
class BestiaryApi
{
    /** @var array{attributes: array<string, int>} $autoLeveling */
    private array $autoLeveling = ['attributes' => []];

    /** @var array<string, EntityInterface> $bestiary */
    private array $bestiary = [];

    public function __construct(
        private readonly Randomizer $randomizer,
        private readonly EntityFactory $entityFactory,
        private readonly ItemsApi $items,
    ) {}

    /**
     * @phpstan-param BestiaryFileData $data
     */
    public function load(array $data): void
    {
        $this->autoLeveling = $data['autoLeveling'];

        $this->bestiary = [];
        foreach ($data['list'] as $partialData) {
            $fullData = $this->fromBestiary($partialData);
            $entity   = $this->entityFactory->from($fullData);
            $this->bestiary[\strtolower($entity->getName())] = $entity;
        }
    }

    public function get(string $name, bool $asClone = true, int $autoLevel = 0): EntityInterface
    {
        $lowerCaseName = \strtolower($name);
        if (!isset($this->bestiary[$lowerCaseName])) {
            throw new BestiaryApiException("Entity '$name' not found in bestiary.", 1701);
        }

        $entity = $asClone ? $this->bestiary[$lowerCaseName]->clone() : $this->bestiary[$lowerCaseName];

        if ($autoLevel !== 0) {
            foreach ($this->autoLeveling['attributes'] as $attributeName => $value) {
                $value   = $autoLevel * $value;
                $entity->getAttributes()->get($attributeName)?->increase($value);
            }

        }

        return $entity;
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
        /** @var BestiaryFileData $data */
        $data = [
            'autoLeveling' => $this->autoLeveling,
            'list'         => [],
        ];

        try {
            foreach ($this->bestiary as $entity) {
                $bestiaryData = [
                    'name'      => $entity->getName(),
                    'type'      => $entity->getType(),
                    'race'      => $entity->getInfo()->race,
                    'size'      => $entity->getInfo()->size,
                    'level'     => $entity->getInfo()->level,
                    'gender'    => $entity->getInfo()->gender,
                    'damages'   => $entity->getDamages()->jsonSerialize(),
                    'attributes' => [
                        'strength'  => $entity->getAttributes()->get('strength')?->getValue() ?? 0,
                        'endurance' => $entity->getAttributes()->get('endurance')?->getValue() ?? 0,
                        'agility'   => $entity->getAttributes()->get('agility')?->getValue() ?? 0,
                        'intuition' => $entity->getAttributes()->get('intuition')?->getValue() ?? 0,
                    ],
                ];

                if ($entity->getEquipment() !== null) {
                    $bestiaryData['equipment'] = $entity->getEquipment()->jsonSerialize();
                }

                if ($entity->getLoot() !== null) {
                    $bestiaryData['loot'] = $entity->getLoot()->jsonSerialize();
                }

                //~ Remove unnecessary data
                if ($bestiaryData['level'] === 1) {
                    unset($bestiaryData['level']);
                }

                if ($bestiaryData['gender'] === 'unknown') {
                    unset($bestiaryData['gender']);
                }

                $data['list'][] = $bestiaryData;
            }

            return \json_encode($data, flags: \JSON_THROW_ON_ERROR | \JSON_PRESERVE_ZERO_FRACTION | ($prettyPrint ? \JSON_PRETTY_PRINT : 0));
            // @codeCoverageIgnoreStart
        } catch (\JsonException $exception) {
            throw new BestiaryApiException('Unable to dump bestiary data: ' . $exception->getMessage(), 1700, $exception);
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
            'level'       => $data['level'] ?? 1,
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

        //~ Build attributes
        $attributes = ['bases' => [], 'compounds' => []];
        foreach ($data['attributes'] as $name => $value) {
            $attributes['bases'][$name] = [
                'type'        => 'base',
                'name'        => $name,
                'initial'     => $value,
                'max'         => $value,
                'value'       => $value,
                'constraints' => ['min' => 0, 'max' => 20],
                'rule'        => null,
            ];
        }
        $attributes['bases']['vitality'] = [
            'type'        => 'base',
            'name'        => 'vitality',
            'initial'     => 0,
            'max'         => 0,
            'value'       => 0,
            'constraints' => ['min' => 0, 'max' => 40],
            'rule'        => 'strength + endurance',
        ];
        $attributes['compounds']['attack']  = ['type' => 'compound', 'name' => 'attack', 'rule' => 'strength + agility'];
        $attributes['compounds']['defense'] = ['type' => 'compound', 'name' => 'defense','rule' => 'endurance + intuition'];

        //~ Initialize empty traits
        $traits  = ['skills' => [], 'states' => [], 'blessings' => [], 'curses' => [], 'titles' => []];

        $loot      = $data['loot'] ?? null;
        $equipment = $data['equipment'] ?? null;

        //~ Build inventory
        $inventory = [
            'coins' => $data['inventory']['coins'] ?? 0,
            'items' => [],
        ];

        foreach ($equipment['weapon'] ?? [] as $equipmentItem) {
            $hitProbability = $this->randomizer->nextFloat();
            if ($hitProbability < $equipmentItem['probability']) {
                $inventory['items'][] = $this->items->get($equipmentItem['name'])->jsonSerialize();
                break;
            }
        }

        //~ Return full data for factory
        $entityData = [
            'name'      => $data['name'],
            'type'      => $data['type'],
            'info'      => $info,
            'damages'   => $damages,
            'attributes' => $attributes,
            'traits'  => $traits,
            'inventory' => $inventory,
        ];

        if ($loot !== null) {
            $entityData['loot'] = $loot;
        }

        if ($equipment !== null) {
            $entityData['equipment'] = $equipment;
        }

        return $entityData;
    }
}
