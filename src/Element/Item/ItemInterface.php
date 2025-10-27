<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Item;

use Velkuns\GameTextEngine\Element\Damage\Damages;
use Velkuns\GameTextEngine\Element\Modifier\Modifier;

/**
 * @phpstan-import-type ModifierData from Modifier
 * @phpstan-import-type DamagesData from Damages
 * @phpstan-type ItemData array{
 *      name: string,
 *      type: string,
 *      subType: string|null,
 *      description: string,
 *      modifiers: list<ModifierData>,
 *      flags: int,
 *      equipped: bool,
 *      damages: DamagesData|null,
 *      quantity?: int,
 *      price: int,
 *  }
 */
interface ItemInterface extends \JsonSerializable
{
    public function getType(): string;

    public function getName(): string;

    public function getDescription(): string;

    public function getSubType(): ?string;

    /**
     * @return list<Modifier>
     */
    public function getModifiers(): array;

    public function getDamages(): Damages;

    public function getFlags(): int;

    public function getPrice(): int;

    public function equipped(): bool;

    public function isConsumable(): bool;

    public function isEquipable(): bool;

    public function isWeapon(): bool;

    public function isGear(): bool;

    public function getQuantity(): int;

    public function setQuantity(int $quantity): self;

    /**
     * @return ItemData
     */
    public function jsonSerialize(): array;

    public function clone(): self;
}
