<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Item\Traits;

trait ItemEquipableTrait
{
    public bool $isEquipped = false;

    public function isEquipped(): bool
    {
        return $this->isEquipped;
    }

    public function equip(): static
    {
        $this->isEquipped = true;

        return $this;
    }

    public function unequip(): static
    {
        $this->isEquipped = false;

        return $this;
    }
}
