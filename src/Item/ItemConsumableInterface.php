<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Item;

use Velkuns\GameTextEngine\Character\Character;

interface ItemConsumableInterface
{
    public function useOn(Character $character): static;
}
