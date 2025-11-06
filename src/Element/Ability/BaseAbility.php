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
 * @phpstan-import-type ConstraintsAbilityData from ConstraintsAbility
 * @phpstan-type BaseAbilityData array{
 *     type: 'base',
 *     name: string,
 *     initial: int,
 *     value: int,
 *     max: int,
 *     constraints: ConstraintsAbilityData,
 *     rule: string|null,
 * }
 */
class BaseAbility implements AbilityInterface
{
    /**
     * @param array<string, BaseAbility> $abilities
     */
    public function __construct(
        public readonly string $name,
        public int $value = 0,
        public int $max = 0,
        public readonly ConstraintsAbility $constraints = new ConstraintsAbility(0, 12),
        public int $initial = 0,
        public readonly ?string $rule = null,
        public readonly array $abilities = [], // List of related abilities
    ) {
        if ($initial === 0) {
            //~ Initialize ability values when not set
            if ($rule === null || $this->abilities === []) {
                throw new \UnexpectedValueException('Ability rule nor related abilities cannot be empty for init.');
            }
            $result = \array_sum(\array_map(fn(BaseAbility $ability) => $ability->getInitial(), $this->abilities));
            $this->initial = $result;
            $this->value = $result;
            $this->max     = $result;
        }
    }

    public function getType(): AbilityType
    {
        return AbilityType::Base;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getMax(): int
    {
        return $this->max;
    }

    public function getInitial(): int
    {
        return $this->initial;
    }

    public function getConstraints(): ConstraintsAbility
    {
        return $this->constraints;
    }

    public function getRule(): ?string
    {
        return $this->rule;
    }

    public function decrease(int $value): self
    {
        $this->value = $this->constraints->clamp($this->value - $value);

        return $this;
    }

    public function increase(int $value): self
    {
        $this->value = $this->constraints->clamp($this->value + $value);

        return $this;
    }

    public function decreaseMax(int $value): self
    {
        $this->max = $this->constraints->clamp($this->max - $value);

        return $this;
    }

    public function increaseMax(int $value): self
    {
        $this->max = $this->constraints->clamp($this->max + $value);

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
            if (!\str_contains($modifier->type, '.ability.' . $this->name)) {
                continue;
            }

            $value += $modifier->value;
        }

        return $this->getConstraints()->clamp($value);
    }

    /**
     * @return BaseAbilityData
     */
    public function jsonSerialize(): array
    {
        return [
            'type'        => AbilityType::Base->value,
            'name'        => $this->name,
            'initial'     => $this->initial,
            'max'         => $this->max,
            'value'       => $this->value,
            'constraints' => $this->constraints->jsonSerialize(),
            'rule'        => $this->rule,
        ];
    }

    public function clone(): self
    {
        return new self(
            name: $this->name,
            value: $this->value,
            max: $this->max,
            constraints: new ConstraintsAbility($this->constraints->min, $this->constraints->max),
            initial: $this->initial,
            rule: $this->rule,
            abilities: $this->abilities,
        );
    }
}
