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
use Velkuns\GameTextEngine\Element\Status\StatusType;

/**
 * @phpstan-import-type EntityData from EntityInterface
 */
class Entity implements EntityInterface
{
    public function __construct(
        private readonly string $name,
        private readonly EntityType $type,
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

    public function getType(): EntityType
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

    public function hasStatus(StatusType $statusType, string $statusName): bool
    {
        return match ($statusType) {
            StatusType::Skill => isset($this->statuses->skills[$statusName]),
            StatusType::State => isset($this->statuses->states[$statusName]),
            StatusType::Blessing => isset($this->statuses->blessings[$statusName]),
            StatusType::Curse => isset($this->statuses->curses[$statusName]),
            StatusType::Title => isset($this->statuses->titles[$statusName]),
        };
    }

    /**
     * @return list<Modifier>
     */
    public function getModifiers(Entity $enemy): array
    {
        $modifiers = [];

        foreach ($this->statuses->skills as $status) {
            if ($status->getModifiers() === []) {
                continue;
            }

            $conditions = $status->getConditions();
            if ($conditions === null || !$conditions->evaluate($this, $enemy)) {
                continue;
            }

            $modifiers = \array_merge($modifiers, $status->getModifiers());
        }

        return $modifiers;
    }

    /**
     * @return EntityData
     */
    public function jsonSerialize(): array
    {
        return [
            'name'      => $this->name,
            'type'      => $this->type->value,
            'coins'     => $this->coins,
            'info'      => $this->info->jsonSerialize(),
            'abilities' => $this->abilities->jsonSerialize(),
            'statuses'  => $this->statuses->jsonSerialize(),
            'inventory' => $this->inventory->jsonSerialize(),
        ];
    }
}
