<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Log;

use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;

readonly class XpLog implements \Stringable
{
    public function __construct(
        public EntityInterface $player,
        public EntityInterface $enemy,
        public int $xp,
    ) {}

    public function __toString(): string
    {
        return "You gain $this->xp XP from {$this->enemy->getName()} kill.";
    }
}
