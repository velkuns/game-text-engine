<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Entity;

use Velkuns\GameTextEngine\Element\Exception\InventoryException;
use Velkuns\GameTextEngine\Element\Item\Item;
use Velkuns\GameTextEngine\Element\Item\ItemInterface;

/**
 * @phpstan-import-type ItemData from Item
 */
class EntityInventory implements \JsonSerializable
{
    /** @var \WeakMap<ItemInterface, ItemInterface> */
    public \WeakMap $items;

    /**
     * @param list<ItemInterface> $items
     */
    public function __construct(
        array $items = [],
    ) {
        $this->items = new \WeakMap();
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    public function get(string $name): ?ItemInterface
    {
        $name = \strtolower($name);

        foreach ($this->items as $item) {
            if (\strtolower($item->getName()) === $name) {
                return $item;
            }
        }

        return null;
    }

    public function getEquippedWeapon(): ?ItemInterface
    {
        foreach ($this->items as $item) {
            if ($item->isWeapon() && $item->equipped()) {
                return $item;
            }
        }

        return null;
    }

    public function add(ItemInterface $item): void
    {
        $this->items[$item] = $item;
    }

    public function drop(ItemInterface $item): void
    {
        unset($this->items[$item]);
    }

    public function consume(string $itemName): void
    {
        $item = $this->get($itemName);
        if ($item === null) {
            throw new InventoryException('Cannot consume unknown item: ' . $itemName, 1700);
        }

        if ($item->getQuantity() > 1) {
            $item->setQuantity($item->getQuantity() - 1);
        } else {
            $this->drop($item);
        }
    }

    /**
     * @phpstan-return list<ItemData>
     */
    public function jsonSerialize(): array
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[] = $item->jsonSerialize();
        }

        return $items;
    }

    public function clone(): self
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[] = $item->clone();
        }

        return new self($items);
    }
}
