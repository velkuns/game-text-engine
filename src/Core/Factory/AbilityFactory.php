<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Factory;

use Velkuns\GameTextEngine\Rpg\Ability\BaseAbility;
use Velkuns\GameTextEngine\Rpg\Ability\CompoundAbility;
use Velkuns\GameTextEngine\Rpg\Ability\ConstraintsAbility;

/**
 * @phpstan-import-type BaseAbilityData from BaseAbility
 * @phpstan-import-type CompoundAbilityData from CompoundAbility
 */
readonly class AbilityFactory
{
    /**
     * @param array<string, BaseAbilityData> $data
     * @return array<string, BaseAbility>
     */
    public function fromBases(array $data): array
    {
        $withoutInit = \array_filter($data, fn(array $abilityData) => $abilityData['rule'] === null);
        $withInit    = \array_filter($data, fn(array $abilityData) => $abilityData['rule'] !== null);

        //~ First create all base abilities without initialization (no rule)
        $bases = \array_map(fn(array $abilityData) => $this->fromBaseAbility($abilityData), $withoutInit);

        //~ Then create all base abilities with init (with rule for initialization)
        $bases += \array_map(fn(array $abilityData) => $this->fromBaseAbility($abilityData, $bases), $withInit);

        return $bases;
    }

    /**
     * @phpstan-param array<string, CompoundAbilityData> $data
     * @phpstan-param array<string, BaseAbility> $bases
     * @return array<string, CompoundAbility>
     */
    public function fromCompounds(array $data, array $bases): array
    {
        return \array_map(fn(array $abilityData) => $this->fromCompoundAbility($abilityData, $bases), $data);
    }

    /**
     * @phpstan-param BaseAbilityData $data
     * @phpstan-param array<string, BaseAbility> $baseAbilities
     */
    public function fromBaseAbility(array $data, array $baseAbilities = []): BaseAbility
    {
        //~ Filter only related abilities
        $abilities = \array_filter(
            $baseAbilities,
            fn(BaseAbility $ability) => \str_contains($data['rule'] ?? '', $ability->name),
        );

        return new BaseAbility(
            name: $data['name'],
            value: $data['value'],
            max: $data['max'],
            constraints: new ConstraintsAbility(
                min: $data['constraints']['min'],
                max: $data['constraints']['max'],
            ),
            initial: $data['initial'],
            rule: $data['rule'] ?? null,
            abilities: $abilities,
        );
    }

    /**
     * @phpstan-param CompoundAbilityData $data
     * @phpstan-param array<string, BaseAbility> $baseAbilities
     */
    public function fromCompoundAbility(array $data, array $baseAbilities): CompoundAbility
    {
        return new CompoundAbility(
            name: $data['name'],
            rule: $data['rule'],
            abilities: $baseAbilities,
        );
    }
}
