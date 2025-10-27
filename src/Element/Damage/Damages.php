<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Damage;

use Velkuns\GameTextEngine\Element\Exception\DamageException;

/**
 * @phpstan-import-type DamageData from DamageInterface
 * @phpstan-type DamagesData array<string, DamageData>
 */
class Damages
{
    /**
     * @param array<string, DamageInterface> $damages
     */
    public function __construct(
        private array $damages = [],
    ) {}

    public function get(string $type): DamageInterface
    {
        if (!isset($this->damages[$type])) {
            throw new DamageException('Unknown damage type: ' . $type, 1350);
        }

        return $this->damages[$type];
    }

    /**
     * @return array<string, DamageInterface>
     */
    public function getAll(): array
    {
        return $this->damages;
    }

    /**
     * @return DamagesData|null
     */
    public function jsonSerialize(): ?array
    {
        if ($this->damages === []) {
            return null;
        }

        return \array_map(fn(DamageInterface $damage) => $damage->jsonSerialize(), $this->damages);
    }

    public function clone(): self
    {
        return new self(\array_map(fn(DamageInterface $damage) => $damage->clone(), $this->damages));
    }
}
