<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Ability;

/**
 * @phpstan-type ConstraintsAbilityData array{
 *     min: int,
 *     max: int
 * }
 */
readonly class ConstraintsAbility implements \JsonSerializable
{
    public function __construct(public int $min, public int $max) {}

    public function clamp(int $value): int
    {
        if ($value < $this->min) {
            return $this->min;
        }

        if ($value > $this->max) {
            return $this->max;
        }

        return $value;
    }

    /**
     * @return ConstraintsAbilityData
     */
    public function jsonSerialize(): array
    {
        return [
            'min' => $this->min,
            'max' => $this->max,
        ];
    }
}
