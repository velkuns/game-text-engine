<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Item\Traits;

use Velkuns\GameTextEngine\Character\Character;

trait ItemConsumableTrait
{
    public function useOn(Character $character): static
    {
        $character->applyModifiers($this->getModifiers());

        return $this;
    }
}
