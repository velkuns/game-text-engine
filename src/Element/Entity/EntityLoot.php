<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Entity;

/**
 * @phpstan-type EntityLootData array{
 *     xp?: int,
 *     coins?: array{min: int, max: int},
 *     items?: list<array{name: string, drop: float}>
 * }
 */
readonly class EntityLoot implements \JsonSerializable
{
    /**
     * @param array{min: int, max: int}|null $coinsLoot
     * @param list<array{name: string, drop: float}>|null $itemsLoot
     */
    public function __construct(
        public ?int $xp = null,
        public ?array $coinsLoot = null,
        public ?array $itemsLoot = null,
    ) {}

    /**
     * @return EntityLootData
     */
    public function jsonSerialize(): array
    {
        $data = [];

        if ($this->xp !== null) {
            $data['xp'] = $this->xp;
        }

        if ($this->coinsLoot !== null) {
            $data['coins'] = $this->coinsLoot;
        }

        if ($this->itemsLoot !== null) {
            $data['items'] = $this->itemsLoot;
        }

        return $data;
    }

    public function clone(): self
    {
        return new self(
            $this->xp,
            $this->coinsLoot,
            $this->itemsLoot,
        );
    }
}
