<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Factory;

use Velkuns\GameTextEngine\Rpg\Attribute\SimpleAttribute;
use Velkuns\GameTextEngine\Rpg\Attribute\CompoundAttribute;
use Velkuns\GameTextEngine\Rpg\Damages\Damages;
use Velkuns\GameTextEngine\Rpg\Entity\Entity;
use Velkuns\GameTextEngine\Rpg\Entity\EntityAttributes;
use Velkuns\GameTextEngine\Rpg\Entity\EntityEquipment;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInfo;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInventory;
use Velkuns\GameTextEngine\Rpg\Entity\EntityLoot;
use Velkuns\GameTextEngine\Rpg\Entity\EntityTraits;
use Velkuns\GameTextEngine\Rpg\Item\Item;
use Velkuns\GameTextEngine\Rpg\Trait\TraitInterface;

/**
 * @phpstan-import-type SimpleAttributeData from SimpleAttribute
 * @phpstan-import-type CompoundAttributeData from CompoundAttribute
 * @phpstan-import-type EntityData from EntityInterface
 * @phpstan-import-type EntityInfoData from EntityInfo
 * @phpstan-import-type AttributesData from EntityAttributes
 * @phpstan-import-type TraitsData from EntityTraits
 * @phpstan-import-type TraitData from TraitInterface
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
        private TraitFactory $traitFactory,
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
        $traits  = $this->fromEntityTraits($data['traits']);
        $inventory = $this->fromEntityInventory($data['inventory']);
        $loot      = $this->fromEntityLoot($data['loot'] ?? null);
        $equipment = $this->fromEntityEquipment($data['equipment'] ?? null);

        return new Entity($name, $type, $info, $damages, $attributes, $traits, $inventory, $loot, $equipment);
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
        $simples     = $this->attributeFactory->fromSimples($data['simples']);
        $compounds = $this->attributeFactory->fromCompounds($data['compounds'], $simples);

        return new EntityAttributes($simples, $compounds);
    }

    /**
     * @phpstan-param DamagesData $data
     */
    private function fromEntityDamages(array $data): Damages
    {
        return $this->damageFactory->fromDamages($data);
    }

    /**
     * @phpstan-param TraitsData $data
     */
    private function fromEntityTraits(array $data): EntityTraits
    {
        $traits = [];
        foreach ($data as $type => $list) {
            $traits[$type] = \array_map(fn(array $traitData) => $this->traitFactory->from($traitData), $list);
        }

        return new EntityTraits($traits);
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
