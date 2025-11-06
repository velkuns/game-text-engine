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
 * @phpstan-type DamageData array{type: string, value: int}
 */
interface DamageInterface extends \JsonSerializable
{
    public function getType(): string;

    public function getValue(): int;

    public function decrease(int $value): self;

    public function increase(int $value): self;

    /**
     * @param list<Modifier> $modifiers
     */
    public function getValueWithModifiers(array $modifiers): int;

    /**
     * @phpstan-return DamageData
     */
    public function jsonSerialize(): array;

    public function clone(): self;
}
