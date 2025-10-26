<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Processor;

use Velkuns\GameTextEngine\Element\Entity\EntityInterface;

readonly class TimeProcessor
{
    /**
     * @param EntityInterface[] $entities
     */
    public function processTurnOnAll(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->processTurn($entity);
        }
    }

    public function processTurn(EntityInterface $entity): void
    {
        foreach ($entity->getStatuses()->getAll() as $status) {
            if ($status->getRemainingTurns() === 0) {
                continue;
            }

            $status->decreaseRemainingTurns();
        }
    }
}
