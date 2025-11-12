<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Factory;

use Velkuns\GameTextEngine\Rpg\Attribute\BaseAttribute;
use Velkuns\GameTextEngine\Rpg\Attribute\CompoundAttribute;
use Velkuns\GameTextEngine\Rpg\Damages\Damages;
use Velkuns\GameTextEngine\Rpg\Entity\Entity;
use Velkuns\GameTextEngine\Rpg\Entity\EntityAttributes;
use Velkuns\GameTextEngine\Rpg\Entity\EntityEquipment;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInfo;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInventory;
use Velkuns\GameTextEngine\Rpg\Entity\EntityLoot;
use Velkuns\GameTextEngine\Rpg\Entity\EntityStatuses;
use Velkuns\GameTextEngine\Rpg\Item\Item;
use Velkuns\GameTextEngine\Rpg\Status\StatusInterface;

/**
 * @phpstan-import-type BaseAttributeData from BaseAttribute
 * @phpstan-import-type CompoundAttributeData from CompoundAttribute
 * @phpstan-import-type EntityData from EntityInterface
 * @phpstan-import-type EntityInfoData from EntityInfo
 * @phpstan-import-type AttributesData from EntityAttributes
 * @phpstan-import-type StatusesData from EntityStatuses
 * @phpstan-import-type StatusData from StatusInterface
 * @phpstan-import-type DamagesData from Damages
 * @phpstan-import-type InventoryData from EntityInventory
 * @phpstan-import-type ItemData from Item
 * @phpstan-import-type EntityLootData from EntityLoot
 * @phpstan-import-type EquipmentData from EntityEquipment
 */
readonly class EntityFactory
{
    public function __construct(
        private AttributeFactory $attributeFactory,
        private StatusFactory $statusFactory,
        private ItemFactory $itemFactory,
        private DamageFactory $damageFactory,
        private LootFactory $lootFactory,
    ) {}

    /**
     * @phpstan-param EntityData $data
     */
    public function from(array $data): EntityInterface
    {
        $name      = $data['name'];
        $type      = $data['type'];
        $info      = $this->fromEntityInfo($data['info']);
        $damages   = $this->fromEntityDamages($data['damages'] ?? []);
        $attributes = $this->fromEntityAttributes($data['attributes']);
        $statuses  = $this->fromEntityStatuses($data['statuses']);
        $inventory = $this->fromEntityInventory($data['inventory']);
        $loot      = $this->fromEntityLoot($data['loot'] ?? null);
        $equipment = $this->fromEntityEquipment($data['equipment'] ?? null);

        return new Entity($name, $type, $info, $damages, $attributes, $statuses, $inventory, $loot, $equipment);
    }

    /**
     * @phpstan-param EntityInfoData $data
     */
    private function fromEntityInfo(array $data): EntityInfo
    {
        $level       = $data['level'];
        $xp          = $data['xp'];
        $age         = $data['age'];
        $size        = $data['size'];
        $race        = $data['race'];
        $gender      = $data['gender'];
        $description = $data['description'];
        $background  = $data['background'];
        $notes       = $data['notes'];

        return new EntityInfo($level, $xp, $age, $size, $race, $gender, $description, $background, $notes);
    }

    /**
     * @phpstan-param AttributesData $data
     */
    private function fromEntityAttributes(array $data): EntityAttributes
    {
        $bases     = $this->attributeFactory->fromBases($data['bases']);
        $compounds = $this->attributeFactory->fromCompounds($data['compounds'], $bases);

        return new EntityAttributes($bases, $compounds);
    }

    /**
     * @phpstan-param DamagesData $data
     */
    private function fromEntityDamages(array $data): Damages
    {
        return $this->damageFactory->fromDamages($data);
    }

    /**
     * @phpstan-param StatusesData $data
     */
    private function fromEntityStatuses(array $data): EntityStatuses
    {
        $statuses = [];
        foreach ($data as $type => $list) {
            $statuses[$type] = \array_map(fn(array $statusData) => $this->statusFactory->from($statusData), $list);
        }

        return new EntityStatuses($statuses);
    }

    /**
     * @phpstan-param InventoryData $data
     */
    private function fromEntityInventory(array $data): EntityInventory
    {
        $items = \array_map(fn(array $itemData) => $this->itemFactory->from($itemData), $data['items']);

        return new EntityInventory($data['coins'], $items);
    }

    /**
     * @phpstan-param EntityLootData $data
     */
    private function fromEntityLoot(?array $data): ?EntityLoot
    {
        if ($data === null) {
            return null;
        }

        return $this->lootFactory->from($data);
    }

    /**
     * @phpstan-param EquipmentData $data
     */
    private function fromEntityEquipment(?array $data): ?EntityEquipment
    {
        if ($data === null) {
            return null;
        }

        return new EntityEquipment($data);
    }
}
