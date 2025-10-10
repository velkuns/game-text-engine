<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element;

use Velkuns\GameTextEngine\Element\Ability\BaseAbility;
use Velkuns\GameTextEngine\Element\Ability\CompoundAbility;
use Velkuns\GameTextEngine\Element\Entity\Entity;
use Velkuns\GameTextEngine\Element\Entity\EntityInventory;
use Velkuns\GameTextEngine\Element\Exception\ElementJsonParseException;
use Velkuns\GameTextEngine\Element\Item\Item;
use Velkuns\GameTextEngine\Element\Modifier\Modifier;

/**
 * @phpstan-import-type BaseAbilityData from BaseAbility
 * @phpstan-import-type CompoundAbilityData from CompoundAbility
 * @phpstan-import-type CharacterData from Entity
 * @phpstan-import-type ModifierData from Modifier
 */
readonly class ElementFactory
{
    public function __construct(
        private AbilityFactory $abilityFactory,
    ) {}

    /**
     * @throws ElementJsonParseException
     */
    public function fromJson(string $type, string $jsonString): Entity|Item
    {
        try {
            /** @var array<string, BaseAbilityData|CompoundAbilityData|CharacterData> $json */
            $json = \json_decode($jsonString, true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new ElementJsonParseException('Cannot parse json data from game element', 2010, $exception);
        }

        return match ($type) {
            'character' => $this->newCharacter($id, $json),
            'item'      => $this->newItem($id, $json),
            //'npc'       => $this->newNpc($id, $json),
            //'monster'   => $this->newMonster($id, $json),
            default    => throw new ElementJsonParseException('Unknown element type: ' . $type, 2011),
        };
    }

    /**
     * @param array<string, CharacterData> $data
     */
    public function newCharacter(int $id, array $data): Entity
    {
        return new Entity(
            $id,
            $data['name'] ?? 'Unamed',
            $this->abilityFactory->fromArray($data['abilities'] ?? []),
            new EntityInventory(),
        );
    }

    public function newItem(int $id, array $data): Item
    {
        return new Item(
            $id,
            $data['name'],
            $data['description'] ?? '',
            $this->newModifiers($data['modifiers'] ?? []),
            $data['flags'] ?? 0,
            $data['isEquipped'] ?? false,
        );
    }

    /**
     * @param ModifierData[] $data
     * @return Modifier[]
     */
    public function newModifiers(array $data): array
    {
        $modifiers = [];
        foreach ($data as $modifierData) {
            $modifiers[] = $this->newModifier($modifierData);
        }

        return $modifiers;
    }

    /**
     * @param ModifierData $data
     */
    public function newModifier(array $data): Modifier
    {
        return new Modifier(
            targetAbility: $data['targetAbility'],
            value: $data['value'],
            name: $data['name'],
            description: $data['description'],
        );
    }
}
