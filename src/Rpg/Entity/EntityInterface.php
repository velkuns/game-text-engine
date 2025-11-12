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
use Velkuns\GameTextEngine\Rpg\Item\ItemInterface;
use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

/**
 * @phpstan-import-type EntityInfoData from EntityInfo
 * @phpstan-import-type DamagesData from Damages
 * @phpstan-import-type AttributesData from EntityAttributes
 * @phpstan-import-type TraitsData from EntityTraits
 * @phpstan-import-type InventoryData from EntityInventory
 * @phpstan-import-type ItemData from ItemInterface
 * @phpstan-import-type EntityLootData from EntityLoot
 * @phpstan-import-type EquipmentData from EntityEquipment
 * @phpstan-type EntityData array{
 *     name: string,
 *     type: string,
 *     info: EntityInfoData,
 *     damages: DamagesData|null,
 *     attributes: AttributesData,
 *     traits: TraitsData,
 *     inventory: InventoryData,
 *     loot?: EntityLootData,
 *     equipment?: EquipmentData
 * }
 */
interface EntityInterface extends \JsonSerializable
{
    public function getName(): string;

    public function getType(): string;

    public function getInfo(): EntityInfo;

    public function getDamages(): Damages;

    public function getAttributes(): EntityAttributes;

    public function getTraits(): EntityTraits;

    public function getInventory(): EntityInventory;

    public function getLoot(): ?EntityLoot;

    public function getEquipment(): ?EntityEquipment;

    public function hasTrait(string $type, string $name): bool;

    public function isAlive(): bool;

    /**
     * @return list<Modifier>
     */
    public function getModifiers(EntityInterface $enemy): array;

    public function clone(): self;

    /**
     * @return EntityData
     */
    public function jsonSerialize(): array;
}
