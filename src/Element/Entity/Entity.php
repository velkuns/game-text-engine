<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Entity;

use Velkuns\GameTextEngine\Element\Damage\Damages;
use Velkuns\GameTextEngine\Element\Modifier\Modifier;

/**
 * @phpstan-import-type EntityData from EntityInterface
 */
readonly class Entity implements EntityInterface
{
    public function __construct(
        public string $name,
        public string $type,
        public EntityInfo $info,
        public Damages $damages,
        public EntityAbilities $abilities,
        public EntityStatuses $statuses,
        public EntityInventory $inventory,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getInfo(): EntityInfo
    {
        return $this->info;
    }

    public function getDamages(): Damages
    {
        return $this->damages;
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

    public function hasStatus(string $type, string $name): bool
    {
        return $this->getStatuses()->getByType($type, $name) !== null;
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
            'name'      => $this->getName(),
            'type'      => $this->getType(),
            'info'      => $this->getInfo()->jsonSerialize(),
            'damages'   => $this->getDamages()->jsonSerialize(),
            'abilities' => $this->getAbilities()->jsonSerialize(),
            'statuses'  => $this->getStatuses()->jsonSerialize(),
            'inventory' => $this->getInventory()->jsonSerialize(),
        ];
    }

    public function clone(): self
    {
        return new self(
            name: $this->getName(),
            type: $this->getType(),
            info: $this->getInfo()->clone(),
            damages: $this->getDamages()->clone(),
            abilities: $this->getAbilities()->clone(),
            statuses: $this->getStatuses()->clone(),
            inventory: $this->getInventory()->clone(),
        );
    }
}
