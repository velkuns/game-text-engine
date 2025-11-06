<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Entity;

use Velkuns\GameTextEngine\Element\Exception\StatusException;
use Velkuns\GameTextEngine\Element\Modifier\Modifier;
use Velkuns\GameTextEngine\Element\Status\StatusInterface;

/**
 * @phpstan-import-type StatusData from StatusInterface
 * @phpstan-type StatusesData array<string, array<string, StatusData>>
 */
class EntityStatuses implements \JsonSerializable
{
    /**
     * @param array<string, array<string, StatusInterface>> $statuses
     */
    public function __construct(
        public array $statuses = [],
    ) {}

    public function getByType(string $type, string $name): ?StatusInterface
    {
        return $this->statuses[$type][$name] ?? null;
    }

    public function set(StatusInterface $status): self
    {
        if (!isset($this->statuses[$status->getType()])) {
            throw new StatusException("Unknown status type '{$status->getType()}'", 1500);
        }

        $this->statuses[$status->getType()][$status->getName()] = $status;

        return $this;
    }

    /**
     * @return array<string, StatusInterface>
     */
    public function getAll(): array
    {
        return \array_merge(...\array_values($this->statuses));
    }

    /**
     * Remove statuses that have duration
     */
    public function clean(): void
    {
        foreach ($this->statuses as $type => $statuses) {
            foreach ($statuses as $name => $status) {
                if ($status->getDurationTurns() > 0) {
                    unset($this->statuses[$type][$name]);
                }
            }
        }
    }

    /**
     * @return array<string, StatusInterface>
     */
    public function getAllFromType(string $type): array
    {
        if (!isset($this->statuses[$type])) {
            throw new StatusException("Unknown status type '$type'", 1501);
        }

        return $this->statuses[$type];
    }

    /**
     * @return list<Modifier>
     */
    public function getAllModifiers(EntityInterface $player, EntityInterface $enemy): array
    {
        $modifiers = [];
        foreach ($this->statuses as $statuses) {
            $modifiers[] = $this->getModifiers($statuses, $player, $enemy);
        }

        return \array_values(\array_merge(...$modifiers));
    }

    /**
     * @param array<string, StatusInterface> $statuses
     * @return Modifier[]
     */
    private function getModifiers(array $statuses, EntityInterface $player, EntityInterface $enemy): array
    {
        $modifiers = [];

        foreach ($statuses as $status) {
            $statusModifiers = $status->getModifiers();
            if ($statusModifiers === [] || !$status->isActive()) {
                continue;
            }

            foreach ($statusModifiers as $modifier) {
                if ($modifier->conditions !== null && !$modifier->conditions->evaluate($player, $enemy)) {
                    continue; // modifier conditions is not met, skip it
                }

                $modifiers[] = $modifier;
            }
        }

        return $modifiers;
    }

    /**
     * @return StatusesData
     */
    public function jsonSerialize(): array
    {
        $data = [];
        foreach ($this->statuses as $type => $statuses) {
            $data[$type] = \array_map(fn(StatusInterface $status) => $status->jsonSerialize(), $statuses);
        }

        return $data;
    }

    public function clone(): self
    {
        $clonedStatuses = [];
        foreach ($this->statuses as $type => $statuses) {
            $clonedStatuses[$type] = \array_map(fn(StatusInterface $status) => $status->clone(), $statuses);
        }

        return new self($clonedStatuses);
    }
}
