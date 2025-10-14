<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Ability;

/**
 * @phpstan-type ConstraintsAbilityData array{
 *     min: int,
 *     max: int
 * }
 */
readonly class ConstraintsAbility implements \JsonSerializable
{
    public function __construct(public int $min, public int $max) {}

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
