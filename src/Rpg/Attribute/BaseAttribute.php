<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Attribute;

use Velkuns\GameTextEngine\Exception\Rpg\AttributeException;
use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

/**
 * @phpstan-import-type ConstraintsAttributeData from ConstraintsAttribute
 * @phpstan-type BaseAttributeData array{
 *     type: 'base',
 *     name: string,
 *     initial: int,
 *     value: int,
 *     max: int,
 *     constraints: ConstraintsAttributeData,
 *     rule: string|null,
 * }
 */
class BaseAttribute implements AttributeInterface
{
    /**
     * @param array<string, BaseAttribute> $attributes
     */
    public function __construct(
        public readonly string $name,
        public int $value = 0,
        public int $max = 0,
        public readonly ConstraintsAttribute $constraints = new ConstraintsAttribute(0, 12),
        public int $initial = 0,
        public readonly ?string $rule = null,
        public readonly array $attributes = [], // List of related attributes
    ) {
        if ($initial === 0) {
            //~ Initialize attribute values when not set
            if ($rule === null || $this->attributes === []) {
                throw new AttributeException('Attribute rule nor related attributes cannot be empty for init.');
            }
            $result = \array_sum(\array_map(fn(BaseAttribute $attribute) => $attribute->getInitial(), $this->attributes));
            $this->initial = $result;
            $this->value   = $result;
            $this->max     = $result;
        }
    }

    public function getType(): AttributeType
    {
        return AttributeType::Base;
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

    public function getConstraints(): ConstraintsAttribute
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
     * Apply modifiers to value attribute value and return a new instance with modified value.
     *
     * @param list<Modifier> $modifiers
     */
    public function getValueWithModifiers(array $modifiers): int
    {
        $value = $this->getValue();
        foreach ($modifiers as $modifier) {
            if (!\str_contains($modifier->type, '.attribute.' . $this->name)) {
                continue;
            }

            $value += $modifier->value;
        }

        return $this->getConstraints()->clamp($value);
    }

    /**
     * @return BaseAttributeData
     */
    public function jsonSerialize(): array
    {
        return [
            'type'        => AttributeType::Base->value,
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
            constraints: new ConstraintsAttribute($this->constraints->min, $this->constraints->max),
            initial: $this->initial,
            rule: $this->rule,
            attributes: $this->attributes,
        );
    }
}
