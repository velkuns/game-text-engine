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
class Status implements StatusInterface
{
    /**
     * @param list<Modifier> $modifiers
     */
    public function __construct(
        private readonly string $type,
        private readonly string $name,
        private readonly string $description,
        private readonly array $modifiers,
        private readonly ?Conditions $conditions = null,
        private readonly int $durationTurns = 0,
        private int $remainingTurns = 0,
    ) {}

    public function getType(): string
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

    public function decreaseRemainingTurns(): self
    {
        $this->remainingTurns = \max($this->remainingTurns - 1, 0);

        return $this;
    }

    /**
     * @return StatusData
     */
    public function jsonSerialize(): array
    {
        return [
            'type'           => $this->getType(),
            'name'           => $this->getName(),
            'description'    => $this->getDescription(),
            'modifiers'      => array_map(fn(Modifier $modifier) => $modifier->jsonSerialize(), $this->getModifiers()),
            'conditions'     => $this->getConditions()?->jsonSerialize(),
            'durationTurns'  => $this->getDurationTurns(),
            'remainingTurns' => $this->getRemainingTurns(),
        ];
    }

    public function clone(): self
    {
        return new self(
            type: $this->type,
            name: $this->name,
            description: $this->description,
            modifiers: array_map(
                fn(Modifier $modifier): Modifier => $modifier->clone(),
                $this->modifiers,
            ),
            conditions: $this->conditions?->clone() ?? null,
            durationTurns: $this->durationTurns,
            remainingTurns: $this->remainingTurns,
        );
    }
}
