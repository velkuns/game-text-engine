<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Character;

use Velkuns\GameTextEngine\Character\Ability\AbilityInterface;
use Velkuns\GameTextEngine\Character\Ability\BaseAbility;
use Velkuns\GameTextEngine\Character\Ability\CompoundAbility;
use Velkuns\GameTextEngine\Modifier\Modifier;

/**
 * @phpstan-import-type CompoundAbilityData from CompoundAbility
 * @phpstan-import-type BaseAbilityData from BaseAbility
 * @phpstan-import-type InventoryData from Inventory
 * @phpstan-type CharacterData array{
 *     id: int,
 *     name: string,
 *     abilities: array<string, BaseAbilityData|CompoundAbility>,
 *     inventory: InventoryData,
 * }
 */
readonly class Character
{
    /**
     * @param array<string, AbilityInterface> $abilities
     */
    public function __construct(
        public int $id,
        public string $name,
        public array $abilities,
        public Inventory $inventory,
    ) {}

    /**
     * @param list<Modifier> $modifiers
     */
    public function applyModifiers(array $modifiers): static
    {
        foreach ($modifiers as $modifier) {
            if (!isset($this->abilities[$modifier->targetAbility])) {
                continue; // Skip if the target ability does not exist
            }

            $ability = $this->abilities[$modifier->targetAbility];
        }

        return $this;
    }

}
