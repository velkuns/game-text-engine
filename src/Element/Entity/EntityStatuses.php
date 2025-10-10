<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Entity;

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
    /** @var array<string, Status> $skills */
    public array $skills = [];

    /** @var array<string, Status> $states */
    public array $states = [];

    /** @var array<string, Status> $blessings */
    public array $blessings = [];

    /** @var array<string, Status> $curses */
    public array $curses = [];

    /** @var array<string, Status> $titles */
    public array $titles = [];

    /**
     * @param array<string, Status> $skills
     * @param array<string, Status> $states
     * @param array<string, Status> $blessings
     * @param array<string, Status> $curses
     * @param array<string, Status> $titles
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

    /**
     * @return StatusesData
     */
    public function jsonSerialize(): array
    {
        return [
            'skills'    => \array_map(fn(Status $skill) => $skill->jsonSerialize(), $this->skills),
            'states'    => \array_map(fn(Status $states) => $states->jsonSerialize(), $this->states),
            'blessings' => \array_map(fn(Status $blessings) => $blessings->jsonSerialize(), $this->blessings),
            'curses'    => \array_map(fn(Status $curses) => $curses->jsonSerialize(), $this->curses),
            'titles'    => \array_map(fn(Status $titles) => $titles->jsonSerialize(), $this->titles),
        ];
    }
}
