<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Processor;

use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;

readonly class TimeProcessor
{
    /**
     * @param EntityInterface[] $entities
     */
    public function turnEndForAll(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->turnEnd($entity);
        }
    }

    public function turnEnd(EntityInterface $entity): void
    {
        foreach ($entity->getAlterations()->getAll() as $alteration) {
            if (!$alteration->isActive() || $alteration->getDuration()->getRemaining() === 0) {
                continue;
            }

            $alteration->getDuration()->decrease();
        }
    }

    /**
     * @param EntityInterface[] $entities
     */
    public function combatEnd(array $entities): void
    {
        foreach ($entities as $entity) {
            $entity->getAlterations()->clean();
        }
    }
}
