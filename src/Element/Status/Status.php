<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Status;

use Velkuns\GameTextEngine\Element\Condition\Conditions;
use Velkuns\GameTextEngine\Element\Modifier\Modifier;

/**
 * @phpstan-import-type StatusData from StatusInterface
 */
readonly class Status implements StatusInterface
{
    /**
     * @param list<Modifier> $modifiers
     */
    public function __construct(
        private StatusType $type,
        private string $name,
        private string $description,
        private array $modifiers,
        private ?Conditions $conditions = null,
        private int $durationTurns = 0,
        private int $remainingTurns = 0,
    ) {}

    public function getType(): StatusType
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getConditions(): ?Conditions
    {
        return $this->conditions;
    }

    /**
     * @return list<Modifier>
     */
    public function getModifiers(): array
    {
        return $this->modifiers;
    }

    public function getDurationTurns(): int
    {
        return $this->durationTurns;
    }

    public function getRemainingTurns(): int
    {
        return $this->remainingTurns;
    }

    /**
     * @return StatusData
     */
    public function jsonSerialize(): array
    {
        return [
            'type'           => $this->getType()->value,
            'name'           => $this->getName(),
            'description'    => $this->getDescription(),
            'conditions'     => $this->getConditions()?->jsonSerialize(),
            'modifiers'      => array_map(fn(Modifier $modifier) => $modifier->jsonSerialize(), $this->getModifiers()),
            'durationTurns'  => $this->getDurationTurns(),
            'remainingTurns' => $this->getRemainingTurns(),
        ];
    }
}
