<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Alteration;

use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

/**
 * @phpstan-import-type AlterationData from AlterationInterface
 */
readonly class Alteration implements AlterationInterface
{
    /**
     * @param list<Modifier> $modifiers
     */
    public function __construct(
        private string $type,
        private string $name,
        private string $description,
        private array $modifiers,
        private AlterationDuration $duration,
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

    public function getDuration(): AlterationDuration
    {
        return $this->duration;
    }

    public function isActive(): bool
    {
        return
            $this->duration->getMax() === 0         // No duration limitation, so still active
            || $this->duration->getRemaining() > 0  // Still have remaining turns, so still active
        ;
    }

    /**
     * @return AlterationData
     */
    public function jsonSerialize(): array
    {
        return [
            'type'        => $this->getType(),
            'name'        => $this->getName(),
            'description' => $this->getDescription(),
            'modifiers'   => array_map(fn(Modifier $modifier) => $modifier->jsonSerialize(), $this->getModifiers()),
            'duration'    => $this->getDuration()->jsonSerialize(),
        ];
    }

    public function clone(): self
    {
        return new self(
            $this->type,
            $this->name,
            $this->description,
            array_map(
                fn(Modifier $modifier): Modifier => $modifier->clone(),
                $this->modifiers,
            ),
            $this->duration->clone(),
        );
    }
}
