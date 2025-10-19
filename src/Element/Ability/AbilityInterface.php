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

interface AbilityInterface extends \JsonSerializable
{
    public function getType(): AbilityType;

    public function getName(): string;

    public function getCurrent(): int;

    public function getMax(): int;

    public function getInitial(): int;

    public function getConstraints(): ConstraintsAbility;

    public function getRule(): ?string;

    public function decrease(int $value): self;

    public function increase(int $value): self;

    public function decreaseMax(int $value): self;

    public function increaseMax(int $value): self;

    /**
     * Apply modifiers to current ability value and return a new instance with modified value.
     *
     * @param list<Modifier> $modifiers
     */
    public function getCurrentWithModifiers(array $modifiers): int;
}
