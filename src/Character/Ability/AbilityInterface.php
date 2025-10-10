<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Character\Ability;

/**
 * @phpstan-type BaseAbilityData non-empty-array<string, array{
 *       type: 'base',
 *       value: int,
 *       initial: int,
 *       range: array{min: int, max: int}
 *     }
 *   >
 */
interface AbilityInterface
{
    public function getName(): string;

    public function getValue(): int;

    public function getInitialValue(): int;

    public function getRangeMin(): int;

    public function getRangeMax(): int;
}
