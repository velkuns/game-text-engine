<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Factory;

use Velkuns\GameTextEngine\Element\Condition\Condition;
use Velkuns\GameTextEngine\Element\Condition\ConditionElementResolver;
use Velkuns\GameTextEngine\Element\Condition\ConditionInterface;
use Velkuns\GameTextEngine\Element\Condition\ConditionParser;
use Velkuns\GameTextEngine\Element\Condition\Conditions;
use Velkuns\GameTextEngine\Element\Condition\ConditionValidatorInterface;

/**
 * @phpstan-import-type ConditionsData from Conditions
 * @phpstan-import-type ConditionData from ConditionInterface
 */
readonly class ConditionsFactory
{
    public function __construct(
        private ConditionParser $parser,
        private ConditionElementResolver $resolver,
        private ConditionValidatorInterface $validator,
    ) {}

    /**
     * @param ConditionsData $data
     */
    public function from(?array $data): ?Conditions
    {
        if ($data === null) {
            return null;
        }

        $numberRequired = $data['numberRequired'];
        $conditions     = \array_map(fn($condition) => $this->fromCondition($condition), $data['conditions']);

        return new Conditions($numberRequired, $conditions);
    }

    /**
     * @param ConditionData $data
     */
    private function fromCondition(array $data): Condition
    {
        $type      = $data['type'];
        $condition = $data['condition'];
        $is        = $data['is'];

        return new Condition($this->parser, $this->resolver, $this->validator, $type, $condition, $is);
    }
}
