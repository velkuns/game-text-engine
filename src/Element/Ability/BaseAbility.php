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
 *     current: int,
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
        public int $current = 0,
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
            $this->current = $result;
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

    public function getCurrent(): int
    {
        return $this->current;
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
        $this->current = $this->constraints->clamp($this->current - $value);

        return $this;
    }

    public function increase(int $value): self
    {
        $this->current = $this->constraints->clamp($this->current + $value);

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
     * Apply modifiers to current ability value and return a new instance with modified value.
     *
     * @param list<Modifier> $modifiers
     */
    public function getCurrentWithModifiers(array $modifiers): int
    {
        $current = $this->getCurrent();
        foreach ($modifiers as $modifier) {
            if ($modifier->ability !== $this->name) {
                continue;
            }

            $current += $modifier->value;
        }

        return $this->getConstraints()->clamp($current);
    }

    /**
     * @return BaseAbilityData
     */
    public function jsonSerialize(): array
    {
        return [
            'type'        => AbilityType::Base->value,
            'name'        => $this->name,
            'current'     => $this->current,
            'max'         => $this->max,
            'constraints' => $this->constraints->jsonSerialize(),
            'initial'     => $this->initial,
            'rule'        => $this->rule,
        ];
    }
}
