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
 * @phpstan-import-type ConditionPartData from ConditionParser
 */
interface ConditionValidatorInterface
{
    /**
     * @param list<ConditionPartData> $conditions
     */
    public function validate(object $element, array $conditions): bool;
}
