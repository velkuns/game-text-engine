<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Damage;

use Velkuns\GameTextEngine\Element\Modifier\Modifier;

/**
 * @phpstan-import-type DamageData from DamageInterface
 */
class Damage implements DamageInterface
{
    public function __construct(
        private readonly string $type,
        private int $value,
    ) {}

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function decrease(int $value): self
    {
        $this->value -= $value;

        return $this;
    }

    public function increase(int $value): self
    {
        $this->value += $value;

        return $this;
    }

    /**
     * @param list<Modifier> $modifiers
     */
    public function getValueWithModifiers(array $modifiers): int
    {
        $value = $this->getValue();
        foreach ($modifiers as $modifier) {
            if (!\str_contains($modifier->type, 'self.damages.' . $this->type)) {
                continue;
            }

            $value += $modifier->value;
        }

        return $value;
    }

    /**
     * @phpstan-return DamageData
     */
    public function jsonSerialize(): array
    {
        return [
            'type'  => $this->getType(),
            'value' => $this->getValue(),
        ];
    }

    public function clone(): self
    {
        return new self(
            type: $this->type,
            value: $this->value,
        );
    }
}
