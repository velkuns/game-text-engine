<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rules\Abilities;

use Velkuns\GameTextEngine\Rpg\Ability\BaseAbility;
use Velkuns\GameTextEngine\Rpg\Ability\CompoundAbility;

/**
 * @phpstan-import-type BaseAbilityData from BaseAbility
 * @phpstan-import-type CompoundAbilityData from CompoundAbility
 * @phpstan-import-type AbilitiesRulesStartingData from AbilitiesRulesStarting
 * @phpstan-import-type AbilitiesRulesLevelingData from AbilitiesRulesLeveling
 * @phpstan-type AbilitiesRulesData array{
 *    description: string,
 *    starting: AbilitiesRulesStartingData,
 *    leveling: AbilitiesRulesLevelingData,
 *    bases: array<string, BaseAbilityData>,
 *    compounds: array<string, CompoundAbilityData>,
 * }
 */
class AbilitiesRules implements \JsonSerializable
{
    /**
     * @param array<string, BaseAbility> $baseAbilities
     * @param array<string, CompoundAbility> $compoundAbilities
     */
    public function __construct(
        public string $description,
        public AbilitiesRulesStarting $starting,
        public AbilitiesRulesLeveling $leveling,
        public array $baseAbilities = [],
        public array $compoundAbilities = [],
    ) {}

    /**
     * @phpstan-return AbilitiesRulesData
     */
    public function jsonSerialize(): array
    {
        //~ Before dump, we need to reset initial/max/value for ability with init rule.
        $baseAbilities = $this->baseAbilities;
        foreach ($baseAbilities as $ability) {
            if ($ability->getRule() === null) {
                continue;
            }

            $ability->value   = 0;
            $ability->initial = 0;
            $ability->max     = 0;
        }

        return [
            'description' => $this->description,
            'starting'    => $this->starting->jsonSerialize(),
            'leveling'    => $this->leveling->jsonSerialize(),
            'bases'       => \array_map(fn(BaseAbility $ability) => $ability->jsonSerialize(), $baseAbilities),
            'compounds'   => \array_map(fn(CompoundAbility $ability) => $ability->jsonSerialize(), $this->compoundAbilities),
        ];
    }
}
