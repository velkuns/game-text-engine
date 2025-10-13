<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Factory;

use Velkuns\GameTextEngine\Element\Ability\BaseAbility;
use Velkuns\GameTextEngine\Element\Ability\CompoundAbility;
use Velkuns\GameTextEngine\Element\Ability\ConstraintsAbility;

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
        return \array_map(fn(array $abilityData) => $this->fromBaseAbility($abilityData), $data);
    }

    /**
     * @param array<string, CompoundAbilityData> $data
     * @param array<string, BaseAbility> $bases
     * @return array<string, CompoundAbility>
     */
    public function fromCompounds(array $data, array $bases): array
    {
        return \array_map(fn(array $abilityData) => $this->fromCompoundAbility($abilityData, $bases), $data);
    }

    /**
     * @param BaseAbilityData $data
     */
    public function fromBaseAbility(array $data): BaseAbility
    {
        return new BaseAbility(
            name: $data['name'],
            current: $data['current'],
            max: $data['max'],
            constraints: new ConstraintsAbility(
                min: $data['constraints']['min'],
                max: $data['constraints']['max'],
            ),
            initial: $data['initial'],
            rule: $data['rule'] ?? null,
        );
    }

    /**
     * @param CompoundAbilityData $data
     * @param array<string, BaseAbility> $baseAbilities
     */
    public function fromCompoundAbility(array $data, array $baseAbilities): CompoundAbility
    {
        $abilities = [];
        foreach ($baseAbilities as $baseAbility) {
            if (\str_contains($data['rule'], $baseAbility->name)) {
                $abilities[$baseAbility->name] = $baseAbility;
            }
        }

        return new CompoundAbility(
            name: $data['name'],
            rule: $data['rule'],
            abilities: $abilities,
        );
    }
}
