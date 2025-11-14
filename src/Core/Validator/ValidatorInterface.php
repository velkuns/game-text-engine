<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Validator;

use Velkuns\GameTextEngine\Core\Prerequisite\ConditionParser;

/**
 * @phpstan-import-type ConditionPartData from ConditionParser
 */
interface ValidatorInterface
{
    public function supports(string $type): bool;

    /**
     * @param list<ConditionPartData> $conditions
     */
    public function validate(string $type, object $element, array $conditions): bool;
}
