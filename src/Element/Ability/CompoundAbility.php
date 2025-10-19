<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Ability;

use Velkuns\GameTextEngine\Element\Modifier\Modifier;

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
    public function getValue(): int
    {
        return \array_sum(\array_map(fn(BaseAbility $ability) => $ability->getValue(), $this->abilities));
    }

    public function getMax(): int
    {
        return \array_sum(\array_map(fn(BaseAbility $ability) => $ability->getMax(), $this->abilities));
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

    public function getRule(): string
    {
        return $this->rule;
    }

    /**
     * @codeCoverageIgnore
     */
    public function decrease(int $value): self
    {
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function increase(int $value): self
    {
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function decreaseMax(int $value): self
    {
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function increaseMax(int $value): self
    {
        return $this;
    }

    /**
     * Apply modifiers to value ability value and return a new instance with modified value.
     *
     * @param list<Modifier> $modifiers
     */
    public function getValueWithModifiers(array $modifiers): int
    {
        $value = $this->getValue();
        foreach ($modifiers as $modifier) {
            if ($modifier->ability !== $this->name) {
                continue;
            }

            $value += $modifier->value;
        }

        return $this->getConstraints()->clamp($value);
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
