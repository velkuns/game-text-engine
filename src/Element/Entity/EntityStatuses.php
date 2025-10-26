<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Entity;

use Velkuns\GameTextEngine\Element\Modifier\Modifier;
use Velkuns\GameTextEngine\Element\Status\Status;
use Velkuns\GameTextEngine\Element\Status\StatusInterface;

/**
 * @phpstan-import-type StatusData from StatusInterface
 * @phpstan-type StatusesData array{
 *     skills: array<string, StatusData>,
 *     states: array<string, StatusData>,
 *     blessings: array<string, StatusData>,
 *     curses: array<string, StatusData>,
 *     titles: array<string, StatusData>,
 * }
 */
class EntityStatuses implements \JsonSerializable
{
    /** @var array<string, StatusInterface> $skills */
    public array $skills = [];

    /** @var array<string, StatusInterface> $states */
    public array $states = [];

    /** @var array<string, StatusInterface> $blessings */
    public array $blessings = [];

    /** @var array<string, StatusInterface> $curses */
    public array $curses = [];

    /** @var array<string, StatusInterface> $titles */
    public array $titles = [];

    /**
     * @param array<string, StatusInterface> $skills
     * @param array<string, StatusInterface> $states
     * @param array<string, StatusInterface> $blessings
     * @param array<string, StatusInterface> $curses
     * @param array<string, StatusInterface> $titles
     */
    public function __construct(
        array $skills = [],
        array $states = [],
        array $blessings = [],
        array $curses = [],
        array $titles = [],
    ) {
        $this->skills    = $skills;
        $this->states    = $states;
        $this->blessings = $blessings;
        $this->curses    = $curses;
        $this->titles    = $titles;
    }

    public function set(string $statusType, StatusInterface $status): self
    {
        match ($statusType) {
            'skill'    => $this->skills[$status->getName()] = $status,
            'state'    => $this->states[$status->getName()] = $status,
            'blessing' => $this->blessings[$status->getName()] = $status,
            'curse'    => $this->curses[$status->getName()] = $status,
            'title'    => $this->titles[$status->getName()] = $status,
            default    => null,
        };

        return $this;
    }

    /**
     * @return list<StatusInterface>
     */
    public function getAll(): array
    {
        return \array_merge(
            \array_values($this->skills),
            \array_values($this->states),
            \array_values($this->blessings),
            \array_values($this->curses),
            \array_values($this->titles),
        );
    }

    /**
     * @return list<Modifier>
     */
    public function getAllModifiers(EntityInterface $player, EntityInterface $enemy): array
    {
        return \array_values(
            \array_merge(
                $this->getModifiers($this->skills, $player, $enemy),
                $this->getModifiers($this->states, $player, $enemy),
                $this->getModifiers($this->blessings, $player, $enemy),
                $this->getModifiers($this->curses, $player, $enemy),
                $this->getModifiers($this->titles, $player, $enemy),
            ),
        );
    }

    /**
     * @param array<string, StatusInterface> $statuses
     * @return Modifier[]
     */
    private function getModifiers(array $statuses, EntityInterface $player, EntityInterface $enemy): array
    {
        $modifiers = [];

        foreach ($statuses as $status) {
            if (
                $status->getModifiers() === []
                || ($status->getRemainingTurns() === 0 && $status->getDurationTurns() > 0)
            ) {
                continue;
            }

            $conditions = $status->getConditions();
            if ($conditions !== null && !$conditions->evaluate($player, $enemy)) {
                continue;
            }

            $modifiers = \array_merge($modifiers, $status->getModifiers());
        }

        return $modifiers;
    }

    /**
     * @return StatusesData
     */
    public function jsonSerialize(): array
    {
        return [
            'skills'    => \array_map(fn(StatusInterface $skill) => $skill->jsonSerialize(), $this->skills),
            'states'    => \array_map(fn(StatusInterface $states) => $states->jsonSerialize(), $this->states),
            'blessings' => \array_map(fn(StatusInterface $blessings) => $blessings->jsonSerialize(), $this->blessings),
            'curses'    => \array_map(fn(StatusInterface $curses) => $curses->jsonSerialize(), $this->curses),
            'titles'    => \array_map(fn(StatusInterface $titles) => $titles->jsonSerialize(), $this->titles),
        ];
    }

    public function clone(): self
    {
        return new self(
            skills: \array_map(fn(StatusInterface $status) => $status->clone(), $this->skills),
            states: \array_map(fn(StatusInterface $status) => $status->clone(), $this->states),
            blessings: \array_map(fn(StatusInterface $status) => $status->clone(), $this->blessings),
            curses: \array_map(fn(StatusInterface $status) => $status->clone(), $this->curses),
            titles: \array_map(fn(StatusInterface $status) => $status->clone(), $this->titles),
        );
    }
}
