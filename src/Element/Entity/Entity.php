<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Entity;

use Velkuns\GameTextEngine\Element\Modifier\Modifier;

/**
 * @phpstan-import-type EntityData from EntityInterface
 */
class Entity implements EntityInterface
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public int $coins,
        public readonly EntityInfo $info,
        public readonly EntityAbilities $abilities,
        public readonly EntityStatuses $statuses,
        public readonly EntityInventory $inventory,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCoins(): int
    {
        return $this->coins;
    }

    public function getInfo(): EntityInfo
    {
        return $this->info;
    }

    public function getAbilities(): EntityAbilities
    {
        return $this->abilities;
    }

    public function getStatuses(): EntityStatuses
    {
        return $this->statuses;
    }

    public function getInventory(): EntityInventory
    {
        return $this->inventory;
    }

    public function hasStatus(string $statusType, string $statusName): bool
    {
        return match ($statusType) {
            'skill'    => isset($this->statuses->skills[$statusName]),
            'state'    => isset($this->statuses->states[$statusName]),
            'blessing' => isset($this->statuses->blessings[$statusName]),
            'curse'    => isset($this->statuses->curses[$statusName]),
            'title'    => isset($this->statuses->titles[$statusName]),
            default    => false, // Unknown status type
        };
    }

    public function isAlive(): bool
    {
        return ($this->abilities->get('vitality')?->getValue() ?? 0) > 0;
    }

    /**
     * @return list<Modifier>
     */
    public function getModifiers(EntityInterface $enemy): array
    {
        return $this->statuses->getAllModifiers($this, $enemy);
    }

    /**
     * @return EntityData
     */
    public function jsonSerialize(): array
    {
        return [
            'name'      => $this->name,
            'type'      => $this->type,
            'coins'     => $this->coins,
            'info'      => $this->info->jsonSerialize(),
            'abilities' => $this->abilities->jsonSerialize(),
            'statuses'  => $this->statuses->jsonSerialize(),
            'inventory' => $this->inventory->jsonSerialize(),
        ];
    }

    public function clone(): self
    {
        return new self(
            name: $this->name,
            type: $this->type,
            coins: $this->coins,
            info: $this->info->clone(),
            abilities: $this->abilities->clone(),
            statuses: $this->statuses->clone(),
            inventory: $this->inventory->clone(),
        );
    }
}
