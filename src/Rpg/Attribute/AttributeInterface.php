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

interface AttributeInterface extends \JsonSerializable
{
    public function getType(): AttributeType;

    public function getName(): string;

    public function getValue(): int;

    public function getMax(): int;

    public function getInitial(): int;

    public function getConstraints(): ConstraintsAttribute;

    public function getRule(): ?string;

    public function decrease(int $value): self;

    public function increase(int $value): self;

    public function decreaseMax(int $value): self;

    public function increaseMax(int $value): self;

    /**
     * Apply modifiers to value attribute value and return a new instance with modified value.
     *
     * @param list<Modifier> $modifiers
     */
    public function getValueWithModifiers(array $modifiers): int;

    public function clone(): self;
}
