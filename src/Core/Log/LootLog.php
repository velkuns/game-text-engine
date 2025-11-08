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

readonly class LootLog implements \Stringable
{
    /**
     * @param list<ItemInterface> $items
     */
    public function __construct(
        public EntityInterface $player,
        public EntityInterface $enemy,
        public int $coins,
        public array $items,
    ) {}

    public function __toString(): string
    {
        $coinsText = '';
        if ($this->coins > 0) {
            $coinsText = "$this->coins coins";
        }

        $itemsText = '';
        if ($this->items !== []) {
            $names = \array_map(fn(ItemInterface $item) => $item->getName(), $this->items);
            $itemsText = implode(', ', $names) . " items";
        }

        return match (true) {
            $coinsText !== '' && $itemsText !== '' => "You found $coinsText and $itemsText on {$this->enemy->getName()}.",
            $coinsText !== '' && $itemsText === '' => "You found $coinsText on {$this->enemy->getName()}.", // @codeCoverageIgnore
            $coinsText === '' && $itemsText !== '' => "You found $itemsText on {$this->enemy->getName()}.", // @codeCoverageIgnore
            default => "You found nothing on {$this->enemy->getName()}.",
        };
    }
}
