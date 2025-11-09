<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Damages;

use Velkuns\GameTextEngine\Exception\Rpg\DamageException;
use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

/**
 * @phpstan-import-type DamageData from DamagesDetailInterface
 * @phpstan-type DamagesData array<string, DamageData>
 */
readonly class Damages
{
    /**
     * @param array<string, DamagesDetailInterface> $damages
     */
    public function __construct(
        private array $damages = [],
    ) {}

    public function get(string $type): DamagesDetailInterface
    {
        if (!isset($this->damages[$type])) {
            throw new DamageException('Unknown damage type: ' . $type, 1350);
        }

        return $this->damages[$type];
    }

    /**
     * @return array<string, DamagesDetailInterface>
     */
    public function getAll(): array
    {
        return $this->damages;
    }

    public function getTotal(): int
    {
        return \array_sum(\array_map(fn(DamagesDetailInterface $detail) => $detail->getValue(), $this->damages));
    }

    /**
     * @param list<Modifier> $modifiers
     */
    public function getTotalWithModifiers(array $modifiers = []): int
    {
        return \array_sum(\array_map(fn(DamagesDetailInterface $detail) => $detail->getValueWithModifiers($modifiers), $this->damages));
    }

    /**
     * @return DamagesData|null
     */
    public function jsonSerialize(): ?array
    {
        if ($this->damages === []) {
            return null;
        }

        return \array_map(fn(DamagesDetailInterface $damage) => $damage->jsonSerialize(), $this->damages);
    }

    public function clone(): self
    {
        return new self(\array_map(fn(DamagesDetailInterface $damage) => $damage->clone(), $this->damages));
    }
}
