<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element;

use Velkuns\GameTextEngine\Character\Ability\AbilityInterface;
use Velkuns\GameTextEngine\Character\Ability\BaseAbility;
use Velkuns\GameTextEngine\Character\Ability\CompoundAbility;

/**
 * @phpstan-import-type BaseAbilityData from BaseAbility
 * @phpstan-import-type CompoundAbilityData from CompoundAbility
 */
readonly class AbilityFactory
{
    /**
     * @param array<string, BaseAbilityData|CompoundAbilityData> $data
     * @return array<string, AbilityInterface>
     */
    public function from(array $data): array
    {
        //~ Process base abilities first, to use after in compound abilities
        /** @var array<string, BaseAbilityData> $baseAbilitiesJson */
        $baseAbilitiesJson     = \array_filter($data, fn(array $part) => $part['type'] === 'base');

        $baseAbilities = [];
        foreach ($baseAbilitiesJson as $name => $baseAbilityJson) {
            $baseAbilities[$name] = $this->newBaseAbility($name, $baseAbilityJson);
        }

        //~ Process compound abilities
        /** @var array<string, CompoundAbilityData> $compoundAbilitiesJson */
        $compoundAbilitiesJson = \array_filter($data, fn(array $part) => $part['type'] === 'compound');

        $compoundAbilities = [];
        foreach ($compoundAbilitiesJson as $name => $compoundAbilityJson) {
            $compoundAbilities[$name] = $this->newCompoundAbility($name, $compoundAbilityJson, $baseAbilities);
        }

        return $baseAbilities + $compoundAbilities;
    }

    /**
     * @param BaseAbilityData $ability
     */
    public function newBaseAbility(string $name, array $ability): BaseAbility
    {
        return new BaseAbility(
            name: $name,
            value: $ability['value'],
            initial: $ability['initial'],
            range: $ability['range'],
        );
    }

    /**
     * @param CompoundAbilityData $ability
     * @param BaseAbility[] $baseAbilities
     */
    public function newCompoundAbility(string $name, array $ability, array $baseAbilities): CompoundAbility
    {
        $abilities = [];
        foreach ($baseAbilities as $baseAbility) {
            if (\str_contains($ability['rule'], $baseAbility->name)) {
                $abilities[$baseAbility->name] = $baseAbility;
            }
        }

        return new CompoundAbility(
            name: $name,
            rule: $ability['rule'],
            abilities: $abilities,
        );
    }
}
