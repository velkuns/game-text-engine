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
        private readonly string $name,
        private readonly string $type,
        private int $coins,
        private readonly EntityInfo $info,
        private readonly EntityAbilities $abilities,
        private readonly EntityStatuses $statuses,
        private readonly EntityInventory $inventory,
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

    /**
     * @return list<Modifier>
     */
    public function getModifiers(Entity $enemy): array
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
}
