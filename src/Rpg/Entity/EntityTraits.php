<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Entity;

use Velkuns\GameTextEngine\Exception\Rpg\TraitException;
use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;
use Velkuns\GameTextEngine\Rpg\Traits\TraitInterface;

/**
 * @phpstan-import-type TraitData from TraitInterface
 * @phpstan-type TraitsData array<string, array<string, TraitData>>
 */
class EntityTraits implements \JsonSerializable
{
    /**
     * @param array<string, array<string, TraitInterface>> $traits
     */
    public function __construct(
        public array $traits = [],
    ) {}

    public function getByType(string $type, string $name): ?TraitInterface
    {
        return $this->traits[$type][$name] ?? null;
    }

    public function set(TraitInterface $trait): self
    {
        if (!isset($this->traits[$trait->getType()])) {
            throw new TraitException("Unknown trait type '{$trait->getType()}'", 1500);
        }

        $this->traits[$trait->getType()][$trait->getName()] = $trait;

        return $this;
    }

    /**
     * @return array<string, TraitInterface>
     */
    public function getAll(): array
    {
        return \array_merge(...\array_values($this->traits));
    }

    /**
     * Remove traits that have duration
     */
    public function clean(): void
    {
        foreach ($this->traits as $type => $traits) {
            foreach ($traits as $name => $trait) {
                if ($trait->getDurationTurns() > 0) {
                    unset($this->traits[$type][$name]);
                }
            }
        }
    }

    /**
     * @return array<string, TraitInterface>
     */
    public function getAllFromType(string $type): array
    {
        if (!isset($this->traits[$type])) {
            throw new TraitException("Unknown trait type '$type'", 1501);
        }

        return $this->traits[$type];
    }

    /**
     * @return list<Modifier>
     */
    public function getAllModifiers(EntityInterface $player, EntityInterface $enemy): array
    {
        $modifiers = [];
        foreach ($this->traits as $traits) {
            $modifiers[] = $this->getModifiers($traits, $player, $enemy);
        }

        return \array_values(\array_merge(...$modifiers));
    }

    /**
     * @param array<string, TraitInterface> $traits
     * @return Modifier[]
     */
    private function getModifiers(array $traits, EntityInterface $player, EntityInterface $enemy): array
    {
        $modifiers = [];

        foreach ($traits as $trait) {
            $traitModifiers = $trait->getModifiers();
            if ($traitModifiers === [] || !$trait->isActive()) {
                continue;
            }

            foreach ($traitModifiers as $modifier) {
                if ($modifier->conditions !== null && !$modifier->conditions->evaluate($player, $enemy)) {
                    continue; // modifier conditions is not met, skip it
                }

                $modifiers[] = $modifier;
            }
        }

        return $modifiers;
    }

    /**
     * @return TraitsData
     */
    public function jsonSerialize(): array
    {
        $data = [];
        foreach ($this->traits as $type => $traits) {
            $data[$type] = \array_map(fn(TraitInterface $trait) => $trait->jsonSerialize(), $traits);
        }

        return $data;
    }

    public function clone(): self
    {
        $clonedTraits = [];
        foreach ($this->traits as $type => $traits) {
            $clonedTraits[$type] = \array_map(fn(TraitInterface $trait) => $trait->clone(), $traits);
        }

        return new self($clonedTraits);
    }
}
