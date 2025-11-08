<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Condition;

use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;

/**
 * @phpstan-import-type ConditionData from ConditionInterface
 * @phpstan-type ConditionsData array{
 *     numberRequired: int,
 *     conditions: list<ConditionData>,
 * }
 */
readonly class Conditions implements \JsonSerializable
{
    /**
     * @param list<Condition> $conditions
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
     * @return list<Condition>
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function evaluate(EntityInterface $player, ?EntityInterface $enemy = null): bool
    {
        $validConditions = 0;

        foreach ($this->conditions as $condition) {
            //~ Some conditions are evaluated on the player, others on the enemy, depending on their type
            $entity = \str_starts_with($condition->getType(), 'self') ? $player : $enemy;

            if ($entity === null) {
                continue; // Cannot evaluate condition without the required entity
            }

            //~ Evaluate condition and increment valid conditions count if true
            $validConditions += $condition->evaluate($entity) ? 1 : 0;

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
            'numberRequired' => $this->numberRequired,
            'conditions'     => \array_map(fn(Condition $condition) => $condition->jsonSerialize(), $this->conditions),
        ];
    }

    public function clone(): self
    {
        return new self(
            $this->numberRequired,
            \array_map(fn(Condition $condition) => $condition->clone(), $this->conditions),
        );
    }
}
