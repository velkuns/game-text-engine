<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Attribute;

use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

/**
 * @phpstan-type CompoundAttributeData array{
 *     type: "compound",
 *     name: string,
 *     rule: string,
 * }
 */
readonly class CompoundAttribute implements AttributeInterface
{
    /** @var array<string, BaseAttribute> $attributes */
    private array $attributes;

    /**
     * @param array<string, BaseAttribute> $attributes
     */
    public function __construct(
        public string $name,
        public string $rule,
        array $attributes,
    ) {
        $this->attributes = \array_filter(
            $attributes,
            fn(BaseAttribute $attribute) => \str_contains($this->rule, $attribute->name),
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): AttributeType
    {
        return AttributeType::Compound;
    }

    /**
     * @todo Implement other rules like multiply, subtract, mixed operations, ...)
     */
    public function getValue(): int
    {
        return \array_sum(\array_map(fn(BaseAttribute $attribute) => $attribute->getValue(), $this->attributes));
    }

    public function getMax(): int
    {
        return \array_sum(\array_map(fn(BaseAttribute $attribute) => $attribute->getMax(), $this->attributes));
    }

    public function getInitial(): int
    {
        return \array_sum(\array_map(fn(BaseAttribute $attribute) => $attribute->getInitial(), $this->attributes));
    }

    public function getConstraints(): ConstraintsAttribute
    {
        $min = \array_sum(\array_map(fn(BaseAttribute $attribute) => $attribute->getConstraints()->min, $this->attributes));
        $max = \array_sum(\array_map(fn(BaseAttribute $attribute) => $attribute->getConstraints()->max, $this->attributes));

        return new ConstraintsAttribute($min, $max);
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
     * @return CompoundAttributeData
     */
    public function jsonSerialize(): array
    {
        return [
            'type' => AttributeType::Compound->value,
            'name' => $this->name,
            'rule' => $this->rule,
        ];
    }

    /**
     * @param array<string, BaseAttribute> $attributes
     */
    public function clone(array $attributes = []): self
    {
        return new self(
            name: $this->name,
            rule: $this->rule,
            attributes: $attributes,
        );
    }
}
