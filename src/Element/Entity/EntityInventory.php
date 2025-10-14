<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Entity;

use Velkuns\GameTextEngine\Element\Item\Item;

/**
 * @phpstan-import-type ItemData from Item
 */
class EntityInventory implements \JsonSerializable
{
    /** @var \WeakMap<Item, Item> */
    public \WeakMap $items;

    /**
     * @param list<Item> $items
     */
    public function __construct(
        array $items = [],
    ) {
        $this->items = new \WeakMap();
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    public function get(string $name): ?Item
    {
        foreach ($this->items as $item) {
            if ($item->getName() === $name) {
                return $item;
            }
        }

        return null;
    }

    public function add(Item $item): void
    {
        $this->items[$item] = $item;
    }

    public function drop(Item $item): void
    {
        unset($this->items[$item]);
    }

    /**
     * @return list<ItemData>
     */
    public function jsonSerialize(): array
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[] = $item->jsonSerialize();
        }

        return $items;
    }
}
