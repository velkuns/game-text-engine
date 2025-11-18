<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Entity;

use Velkuns\GameTextEngine\Rpg\Damages\Damages;
use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

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
        public EntityAttributes $attributes,
        public EntityTraits $traits,
        public EntityAlterations $alterations,
        public EntityInventory $inventory,
        public ?EntityLoot $loot,
        public ?EntityEquipment $equipment,
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

    public function getAttributes(): EntityAttributes
    {
        return $this->attributes;
    }

    public function getTraits(): EntityTraits
    {
        return $this->traits;
    }

    public function getAlterations(): EntityAlterations
    {
        return $this->alterations;
    }

    public function getInventory(): EntityInventory
    {
        return $this->inventory;
    }

    public function getLoot(): ?EntityLoot
    {
        return $this->loot;
    }

    public function getEquipment(): ?EntityEquipment
    {
        return $this->equipment;
    }

    public function hasTrait(string $type, string $name): bool
    {
        return $this->getTraits()->getByType($type, $name) !== null;
    }

    public function hasAlteration(string $type, string $name): bool
    {
        return $this->getAlterations()->getByType($type, $name) !== null;
    }

    public function isAlive(): bool
    {
        return ($this->attributes->get('vitality')?->getValue() ?? 0) > 0;
    }

    /**
     * @return list<Modifier>
     */
    public function getModifiers(EntityInterface $enemy): array
    {
        return array_merge(
            $this->traits->getAllModifiers($this, $enemy),
            $this->alterations->getAllModifiers($this, $enemy),
        );
    }

    /**
     * @return EntityData
     */
    public function jsonSerialize(): array
    {
        $data = [
            'name'        => $this->getName(),
            'type'        => $this->getType(),
            'info'        => $this->getInfo()->jsonSerialize(),
            'damages'     => $this->getDamages()->jsonSerialize(),
            'attributes'  => $this->getAttributes()->jsonSerialize(),
            'traits'      => $this->getTraits()->jsonSerialize(),
            'alterations' => $this->getAlterations()->jsonSerialize(),
            'inventory'   => $this->getInventory()->jsonSerialize(),
        ];

        if ($this->getLoot() !== null) {
            $data['loot'] = $this->getLoot()->jsonSerialize();
        }

        if ($this->getEquipment() !== null) {
            $data['equipment'] = $this->getEquipment()->jsonSerialize();
        }

        return $data;
    }

    public function clone(): self
    {
        return new self(
            name: $this->getName(),
            type: $this->getType(),
            info: $this->getInfo()->clone(),
            damages: $this->getDamages()->clone(),
            attributes: $this->getAttributes()->clone(),
            traits: $this->getTraits()->clone(),
            alterations: $this->getAlterations()->clone(),
            inventory: $this->getInventory()->clone(),
            loot: $this->getLoot()?->clone(),
            equipment: $this->getEquipment()?->clone(),
        );
    }
}
