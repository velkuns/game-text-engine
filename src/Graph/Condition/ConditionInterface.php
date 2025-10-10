<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Graph\Condition;

interface ConditionInterface
{
    /**
     * Evaluate the condition.
     *
     * @param array<mixed> $context The context in which the condition is evaluated.
     * @return bool True if the condition is met, false otherwise.
     */
    public function evaluate(array $context = []): bool;
}
