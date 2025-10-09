<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Character;

use Velkuns\GameTextEngine\Exception\ItemUnequipableException;
use Velkuns\GameTextEngine\Item\Item;

/**
 * @phpstan-import-type ItemData from Item
 * @phpstan-type InventoryData array{
 *     weapon?: ItemData|null,
 *     gears: list<ItemData>,
 *     bag: list<ItemData>,
 * }
 */
class Inventory
{
    /**
     * @param list<Item> $items
     */
    public static function fromItems(array $items): self
    {
        $inventory = new self();
        foreach ($items as $item) {
            if ($item->isWeapon() && $item->isEquipped()) {
                $inventory->equipWeapon($item);
                continue;
            }

            if ($item->isGear() && $item->isEquipped()) {
                $inventory->equipGear($item);
                continue;
            }

            $inventory->addToBag($item);
        }

        return $inventory;
    }

    /**
     * @param Item|null $weapon
     * @param list<Item> $gears
     * @param list<Item> $bag
     */
    public function __construct(
        public ?Item $weapon = null,
        public array $gears = [],
        public array $bag = [],
    ) {}

    public function equipWeapon(Item $item): static
    {
        if (!$item->isWeapon()) {
            throw new ItemUnequipableException('The item is not equipable or not a weapon.');
        }

        $this->weapon = $item;

        return $this;
    }

    public function equipGear(Item $item): static
    {
        if (!$item->isGear()) {
            throw new ItemUnequipableException('The item is not equipable or not a armor.');
        }

        $this->gears[] = $item;

        return $this;
    }

    public function addToBag(Item $item): static
    {
        $this->bag[] = $item;

        return $this;
    }
}
