<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Ability;

/**
 * @phpstan-type CompoundAbilityData array{
 *     type: "compound",
 *     name: string,
 *     rule: string,
 * }
 */
readonly class CompoundAbility implements AbilityInterface
{
    /**
     * @param array<string, BaseAbility> $abilities
     */
    public function __construct(
        public string $name,
        public string $rule,
        private array $abilities,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): AbilityType
    {
        return AbilityType::Compound;
    }

    /**
     * @todo Implement other rules like multiply, subtract, mixed operations, ...)
     */
    public function getCurrent(): int
    {
        return \array_sum(\array_map(fn(BaseAbility $ability) => $ability->getCurrent(), $this->abilities));
    }

    public function getInitial(): int
    {
        return \array_sum(\array_map(fn(BaseAbility $ability) => $ability->getInitial(), $this->abilities));
    }

    public function getConstraints(): ConstraintsAbility
    {
        $min = \array_sum(\array_map(fn(BaseAbility $ability) => $ability->getConstraints()->min, $this->abilities));
        $max = \array_sum(\array_map(fn(BaseAbility $ability) => $ability->getConstraints()->max, $this->abilities));

        return new ConstraintsAbility($min, $max);
    }

    /**
     * @return CompoundAbilityData
     */
    public function jsonSerialize(): array
    {
        return [
            'type' => AbilityType::Compound->value,
            'name' => $this->name,
            'rule' => $this->rule,
        ];
    }
}
