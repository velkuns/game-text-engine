<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Condition;

use Velkuns\GameTextEngine\Element\Entity\EntityInterface;

/**
 * @phpstan-import-type ConditionData from ConditionInterface
 * @phpstan-type ConditionsData array{
 *     number_required: int,
 *     conditions: array<string, ConditionData>,
 * }
 */
readonly class Conditions implements \JsonSerializable
{
    /**
     * @param array<string, Condition> $conditions
     */
    public function __construct(
        private int $numberRequired,
        private array $conditions,
    ) {}

    public function getNumberRequired(): int
    {
        return $this->numberRequired;
    }

    /**
     * @return array<string, Condition>
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function evaluate(EntityInterface $entity): bool
    {
        $validConditions = 0;

        foreach ($this->conditions as $condition) {
            $validConditions += $condition->isValid($entity) ? 1 : 0;

            // Early exit if we already reached the required number of valid conditions
            if ($validConditions >= $this->numberRequired) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return ConditionsData
     */
    public function jsonSerialize(): array
    {
        return [
            'number_required' => $this->numberRequired,
            'conditions'      => \array_map(fn(Condition $condition) => $condition->jsonSerialize(), $this->conditions),
        ];
    }
}
