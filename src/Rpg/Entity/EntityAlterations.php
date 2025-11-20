<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Entity;

use Velkuns\GameTextEngine\Exception\Rpg\AlterationException;
use Velkuns\GameTextEngine\Rpg\Alteration\AlterationInterface;
use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

/**
 * @phpstan-import-type AlterationData from AlterationInterface
 * @phpstan-type EntityAlterationsData array<string, array<string, AlterationData>>
 */
class EntityAlterations implements \JsonSerializable
{
    /**
     * @param array<string, array<string, AlterationInterface>> $alterations
     */
    public function __construct(
        public array $alterations = [],
    ) {}

    public function getByType(string $type, string $name): ?AlterationInterface
    {
        return $this->alterations[$type][$name] ?? null;
    }

    public function set(AlterationInterface $alteration): self
    {
        if (!isset($this->alterations[$alteration->getType()])) {
            throw new AlterationException("Unknown alteration type '{$alteration->getType()}'", 1540);
        }

        $this->alterations[$alteration->getType()][$alteration->getName()] = $alteration;

        return $this;
    }

    /**
     * @return array<string, AlterationInterface>
     */
    public function getAll(): array
    {
        return \array_merge(...\array_values($this->alterations));
    }

    /**
     * Remove traits that have duration
     */
    public function clean(): void
    {
        foreach ($this->alterations as $type => $alterations) {
            foreach ($alterations as $name => $alteration) {
                if ($alteration->getDuration()->getMax() > 0) {
                    unset($this->alterations[$type][$name]);
                }
            }
        }
    }

    /**
     * @return array<string, AlterationInterface>
     */
    public function getAllFromType(string $type): array
    {
        if (!isset($this->alterations[$type])) {
            throw new AlterationException("Unknown trait type '$type'", 1501);
        }

        return $this->alterations[$type];
    }

    /**
     * @return list<Modifier>
     */
    public function getAllModifiers(EntityInterface $player, EntityInterface $enemy): array
    {
        $modifiers = [];
        foreach ($this->alterations as $alterations) {
            $modifiers[] = $this->getModifiers($alterations, $player, $enemy);
        }

        return \array_values(\array_merge(...$modifiers));
    }

    /**
     * @param array<string, AlterationInterface> $alterations
     * @return Modifier[]
     */
    private function getModifiers(array $alterations, EntityInterface $player, EntityInterface $enemy): array
    {
        $modifiers = [];

        foreach ($alterations as $alteration) {
            $alterationModifiers = $alteration->getModifiers();
            if ($alterationModifiers === [] || !$alteration->isActive()) {
                continue;
            }

            foreach ($alterationModifiers as $modifier) {
                if ($modifier->prerequisites !== null && !$modifier->prerequisites->evaluate($player, $enemy)) {
                    continue; // modifier prerequisites is not met, skip it
                }

                $modifiers[] = $modifier;
            }
        }

        return $modifiers;
    }

    /**
     * @return EntityAlterationsData
     */
    public function jsonSerialize(): array
    {
        $data = [];
        foreach ($this->alterations as $type => $alterations) {
            $data[$type] = \array_map(fn(AlterationInterface $alteration) => $alteration->jsonSerialize(), $alterations);
        }

        return $data;
    }

    public function clone(): self
    {
        $clonedAlterations = [];
        foreach ($this->alterations as $type => $alterations) {
            $clonedAlterations[$type] = \array_map(fn(AlterationInterface $alteration) => $alteration->clone(), $alterations);
        }

        return new self($clonedAlterations);
    }
}
