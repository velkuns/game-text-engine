<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Item;

interface ItemEquipableInterface
{
    public function isEquipped(): bool;

    public function equip(): static;

    public function unequip(): static;
}
