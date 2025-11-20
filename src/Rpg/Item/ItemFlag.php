<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Item;

class ItemFlag
{
    public const int BASE = 0;
    public const int CONSUMABLE = 1;
    public const int EQUIPABLE = 2;
    public const int WEAPON = 4;
    public const int SHIELD = 8;
    public const int GEAR = 16;
    public const int ATTACK_MELEE = 32;
    public const int ATTACK_RANGE = 64;
    public const int ATTACK_MAGICAL = 128;
}
