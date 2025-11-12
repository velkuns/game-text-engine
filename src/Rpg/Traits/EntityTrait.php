<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Traits;

use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

/**
 * @phpstan-import-type TraitData from TraitInterface
 */
class EntityTrait implements TraitInterface
{
    /**
     * @param list<Modifier> $modifiers
     */
    public function __construct(
        private readonly string $type,
        private readonly string $name,
        private readonly string $description,
        private readonly array $modifiers,
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

    /**
     * @return list<Modifier>
     */
    public function getModifiers(): array
    {
        return $this->modifiers;
    }

    public function isActive(): bool
    {
        return
            $this->getDurationTurns() === 0    // No duration limitation, so still active
            || $this->getRemainingTurns() > 0  // Still have remaining turns, so still active
        ;
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
     * @return TraitData
     */
    public function jsonSerialize(): array
    {
        $data = [
            'type'           => $this->getType(),
            'name'           => $this->getName(),
            'description'    => $this->getDescription(),
            'modifiers'      => array_map(fn(Modifier $modifier) => $modifier->jsonSerialize(), $this->getModifiers()),
        ];

        if ($this->getDurationTurns() > 0) {
            $data['durationTurns']  = $this->getDurationTurns();
            $data['remainingTurns'] = $this->getRemainingTurns();
        }

        return $data;
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
            durationTurns: $this->durationTurns,
            remainingTurns: $this->remainingTurns,
        );
    }
}
