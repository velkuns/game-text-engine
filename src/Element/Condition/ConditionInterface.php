<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Condition;

/**
 * @phpstan-type ConditionData array{
 *     type: string,
 *     condition: string,
 *     is: bool,
 * }
 */
interface ConditionInterface
{
    public function getType(): string;

    public function getCondition(): string;

    public function is(): bool;

    public function clone(): self;
}
