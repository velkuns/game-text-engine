<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rules\Abilities;

/**
 * @phpstan-type AbilitiesRulesStartingData array{
 *    attributionPoints: int,
 *    attributionPointsMaxPerAbility: int,
 * }
 */
class AbilitiesRulesStarting implements \JsonSerializable
{
    public function __construct(
        public int $attributionPoints,
        public int $attributionPointsMaxPerAbility,
    ) {}

    /**
     * @phpstan-return AbilitiesRulesStartingData
     */
    public function jsonSerialize(): array
    {
        return [
            'attributionPoints'              => $this->attributionPoints,
            'attributionPointsMaxPerAbility' => $this->attributionPointsMaxPerAbility,
        ];
    }
}
