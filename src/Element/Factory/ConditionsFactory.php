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
use Velkuns\GameTextEngine\Element\Condition\ConditionInterface;
use Velkuns\GameTextEngine\Element\Condition\ConditionOperatorType;
use Velkuns\GameTextEngine\Element\Condition\Conditions;
use Velkuns\GameTextEngine\Element\Condition\ConditionType;

/**
 * @phpstan-import-type ConditionsData from Conditions
 * @phpstan-import-type ConditionData from ConditionInterface
 */
readonly class ConditionsFactory
{
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
        $type     = ConditionType::from($data['type']);
        $name     = $data['name'];
        $operator = ConditionOperatorType::from($data['operator']);
        $value    = $data['value'];
        $subType  = $data['subType'] ?? null;
        $equipped = $data['equipped'] ?? null;
        $flags    = $data['flags'] ?? null;
        return new Condition($type, $name, $operator, $value, $subType, $equipped, $flags);
    }
}
