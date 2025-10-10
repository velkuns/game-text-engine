<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Graph\Condition;

use Velkuns\GameTextEngine\Graph\Condition\ConditionInterface;

readonly class AlwaysCondition implements ConditionInterface
{
    public function evaluate(array $context = []): bool
    {
        return true;
    }
}
