<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Item;

use Velkuns\GameTextEngine\Element\Modifier\Modifier;

/**
 * @phpstan-import-type ModifierData from Modifier
 * @phpstan-type ItemData array{
 *      name: string,
 *      type: string,
 *      subtype: string|null,
 *      description: string,
 *      modifiers: list<ModifierData>,
 *      flags: int,
 *      equipped: bool,
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

    public function getFlags(): int;

    public function getPrice(): int;

    public function isEquipped(): bool;


    public function isConsumable(): bool;

    public function isEquipable(): bool;

    public function isWeapon(): bool;

    public function isGear(): bool;
}
