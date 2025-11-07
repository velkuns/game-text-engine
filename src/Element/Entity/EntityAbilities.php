<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Entity;

use Velkuns\GameTextEngine\Element\Ability\AbilityInterface;
use Velkuns\GameTextEngine\Element\Ability\BaseAbility;
use Velkuns\GameTextEngine\Element\Ability\CompoundAbility;

/**
 * @phpstan-import-type BaseAbilityData from BaseAbility
 * @phpstan-import-type CompoundAbilityData from CompoundAbility
 * @phpstan-type AbilitiesData array{
 *     bases: array<string, BaseAbilityData>,
 *     compounds: array<string, CompoundAbilityData>,
 * }
 */
readonly class EntityAbilities implements \JsonSerializable
{
    /**
     * @param array<string, BaseAbility> $bases
     * @param array<string, CompoundAbility> $compounds
     */
    public function __construct(
        public array $bases,
        public array $compounds = [],
    ) {}

    public function get(string $name): ?AbilityInterface
    {
        return $this->bases[$name] ?? $this->compounds[$name] ?? null;
    }

    /**
     * @return AbilitiesData
     */
    public function jsonSerialize(): array
    {
        return [
            'bases'     => \array_map(fn(AbilityInterface $ability) => $ability->jsonSerialize(), $this->bases),
            'compounds' => \array_map(fn(AbilityInterface $ability) => $ability->jsonSerialize(), $this->compounds),
        ];
    }

    public function clone(): self
    {
        $bases     = \array_map(fn(AbilityInterface $ability) => $ability->clone(), $this->bases);
        $compounds = \array_map(fn(AbilityInterface $ability) => $ability->clone($bases), $this->compounds);

        return new self(
            $bases,
            $compounds,
        );
    }
}
