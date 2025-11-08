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
use Velkuns\GameTextEngine\Rpg\Item\ItemInterface;

readonly class CombatLog implements \Stringable
{
    /**
     * @param array<string, string> $debug
     */
    public function __construct(
        public EntityInterface $attacker,
        public EntityInterface $defender,
        public ?ItemInterface $item,
        public int $damages,
        public float $hitChance,
        public float $hitRoll,
        public array $debug = [],
    ) {}

    public function isHit(): bool
    {
        return $this->hitRoll <= $this->hitChance;
    }

    public function __toString(): string
    {
        $withItem = $this->item !== null ? "with {$this->item->getName()}" : 'without weapon';
        if ($this->hitRoll <= $this->hitChance) {
            return "{$this->attacker->getName()} 'hit' {$this->defender->getName()} $withItem and make $this->damages damage(s).";
        }

        return "{$this->attacker->getName()} 'miss' {$this->defender->getName()} $withItem.";
    }
}
