<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Factory;

use Velkuns\GameTextEngine\Element\Ability\BaseAbility;
use Velkuns\GameTextEngine\Element\Ability\CompoundAbility;
use Velkuns\GameTextEngine\Element\Damage\Damages;
use Velkuns\GameTextEngine\Element\Entity\Entity;
use Velkuns\GameTextEngine\Element\Entity\EntityAbilities;
use Velkuns\GameTextEngine\Element\Entity\EntityInfo;
use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Entity\EntityInventory;
use Velkuns\GameTextEngine\Element\Entity\EntityStatuses;
use Velkuns\GameTextEngine\Element\Item\Item;
use Velkuns\GameTextEngine\Element\Status\StatusInterface;

/**
 * @phpstan-import-type BaseAbilityData from BaseAbility
 * @phpstan-import-type CompoundAbilityData from CompoundAbility
 * @phpstan-import-type EntityData from EntityInterface
 * @phpstan-import-type EntityInfoData from EntityInfo
 * @phpstan-import-type AbilitiesData from EntityAbilities
 * @phpstan-import-type StatusesData from EntityStatuses
 * @phpstan-import-type StatusData from StatusInterface
 * @phpstan-import-type DamagesData from Damages
 * @phpstan-import-type InventoryData from EntityInventory
 * @phpstan-import-type ItemData from Item
 */
readonly class EntityFactory
{
    public function __construct(
        private AbilityFactory $abilityFactory,
        private StatusFactory $statusFactory,
        private ItemFactory $itemFactory,
        private DamageFactory $damageFactory,
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
        $abilities = $this->fromEntityAbilities($data['abilities']);
        $statuses  = $this->fromEntityStatuses($data['statuses']);
        $inventory = $this->fromEntityInventory($data['inventory']);

        return new Entity($name, $type, $info, $damages, $abilities, $statuses, $inventory);
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
     * @phpstan-param AbilitiesData $data
     */
    private function fromEntityAbilities(array $data): EntityAbilities
    {
        $bases     = $this->abilityFactory->fromBases($data['bases']);
        $compounds = $this->abilityFactory->fromCompounds($data['compounds'], $bases);

        return new EntityAbilities($bases, $compounds);
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
        $skills    = \array_map(fn(array $statusData) => $this->statusFactory->from($statusData), $data['skills']);
        $states    = \array_map(fn(array $statusData) => $this->statusFactory->from($statusData), $data['states']);
        $blessings = \array_map(fn(array $statusData) => $this->statusFactory->from($statusData), $data['blessings']);
        $curses    = \array_map(fn(array $statusData) => $this->statusFactory->from($statusData), $data['curses']);
        $titles    = \array_map(fn(array $statusData) => $this->statusFactory->from($statusData), $data['titles']);

        return new EntityStatuses($skills, $states, $blessings, $curses, $titles);
    }

    /**
     * @phpstan-param InventoryData $data
     */
    private function fromEntityInventory(array $data): EntityInventory
    {
        $items = \array_map(fn(array $itemData) => $this->itemFactory->from($itemData), $data['items']);

        return new EntityInventory($data['coins'], $items);
    }
}
