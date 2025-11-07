<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rules\Abilities;

use Velkuns\GameTextEngine\Exception\Rules\AbilitiesRulesException;

/**
 * @phpstan-type AbilitiesRulesLevelingData array{
 *    attributionPoints: int,
 *    attributionPointsMaxPerAbility: int,
 * }
 */
class AbilitiesRulesLeveling implements \JsonSerializable
{
    public function __construct(
        public int $attributionPoints,
        public int $attributionPointsMaxPerAbility,
    ) {}

    /**
     * @param array<string, int> $abilities
     */
    public function assertHasCorrectAttribution(array $abilities): void
    {
        $total = 0;
        foreach ($abilities as $value) {
            if ($value > $this->attributionPointsMaxPerAbility) {
                throw new AbilitiesRulesException("Only $this->attributionPointsMaxPerAbility point(s) per ability is allowed for level up.", 2100);
            }

            $total += $value;
        }

        $diff = $this->attributionPoints - $total;
        if ($diff < 0) {
            throw new AbilitiesRulesException("Only $this->attributionPoints point(s) is allowed for level up.", 2101);
        }

        if ($diff > 0) {
            throw new AbilitiesRulesException("Remaining $diff point(s) to attribute to abilities", 2102);
        }
    }

    /**
     * @phpstan-return AbilitiesRulesLevelingData
     */
    public function jsonSerialize(): array
    {
        return [
            'attributionPoints'              => $this->attributionPoints,
            'attributionPointsMaxPerAbility' => $this->attributionPointsMaxPerAbility,
        ];
    }
}
