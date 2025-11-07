<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Validator;

use Velkuns\GameTextEngine\Element\Condition\ConditionParser;
use Velkuns\GameTextEngine\Exception\Element\UnsupportedConditionTypeException;

/**
 * @phpstan-import-type ConditionPartData from ConditionParser
 */
readonly class ValidatorHandler
{
    /**
     * @param list<ValidatorInterface> $validators
     */
    public function __construct(
        private array $validators = [],
    ) {}

    /**
     * @param list<ConditionPartData> $conditions
     */
    public function handle(string $type, object $element, array $conditions): bool
    {
        $type = \substr($type, (int) \strpos($type, '.') + 1); // Remove self.|enemy. prefix
        foreach ($this->validators as $validator) {
            if ($validator->supports($type)) {
                return $validator->validate($type, $element, $conditions);
            }
        }

        throw new UnsupportedConditionTypeException('Unsupported type: ' . $type);
    }
}
