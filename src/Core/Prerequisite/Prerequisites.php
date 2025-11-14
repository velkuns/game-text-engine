<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Prerequisite;

use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;

/**
 * @phpstan-import-type RequirementData from RequirementInterface
 * @phpstan-type PrerequisitesData array{
 *     numberRequired: int,
 *     requirements: list<RequirementData>,
 * }
 */
readonly class Prerequisites implements \JsonSerializable
{
    /**
     * @param list<RequirementInterface> $requirements
     */
    public function __construct(
        private int $numberRequired,
        private array $requirements,
    ) {}

    public function getNumberRequired(): int
    {
        return $this->numberRequired;
    }

    /**
     * @return list<RequirementInterface>
     */
    public function getRequirements(): array
    {
        return $this->requirements;
    }

    public function evaluate(EntityInterface $player, ?EntityInterface $enemy = null): bool
    {
        $validRequirements = 0;

        foreach ($this->requirements as $requirement) {
            //~ Some requirements are evaluated on the player, others on the enemy, depending on their type
            $entity = \str_starts_with($requirement->getType(), 'self') ? $player : $enemy;

            if ($entity === null) {
                continue; // Cannot evaluate requirement without the required entity
            }

            //~ Evaluate requirement and increment valid requirements count if true
            $validRequirements += $requirement->evaluate($entity) ? 1 : 0;

            // Early exit if we already reached the required number of valid requirements
            if ($validRequirements >= $this->numberRequired) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return PrerequisitesData
     */
    public function jsonSerialize(): array
    {
        return [
            'numberRequired' => $this->numberRequired,
            'requirements'   => \array_map(fn(RequirementInterface $requirement) => $requirement->jsonSerialize(), $this->requirements),
        ];
    }

    public function clone(): self
    {
        return new self(
            $this->numberRequired,
            \array_map(fn(RequirementInterface $requirement) => $requirement->clone(), $this->requirements),
        );
    }
}
