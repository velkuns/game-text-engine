<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Element\Entity;

use Velkuns\GameTextEngine\Element\Entity\EntityInventory;
use Velkuns\GameTextEngine\Element\Exception\InventoryException;
use Velkuns\GameTextEngine\Element\Exception\ItemException;
use Velkuns\GameTextEngine\Element\Item\Item;
use Velkuns\GameTextEngine\Element\Item\ItemFlag;
use PHPUnit\Framework\TestCase;

class EntityInventoryTest extends TestCase
{
    public function testDrop(): void
    {
        $item = new Item('The Sword', 'sword');
        $inventory = new EntityInventory(0);
        $inventory->add($item);

        self::assertCount(1, $inventory->items);
        self::assertNotNull($inventory->get('The Sword'));
        self::assertSame($item, $inventory->get('The Sword'));
    }

    public function testGetEquippedWeapon(): void
    {
        $item = new Item('The Sword', 'sword', equipped: true);
        $inventory = new EntityInventory(0);
        $inventory->add($item);

        self::assertNull($inventory->getEquippedWeapon());

        $item2 = new Item('The Sword #2', 'sword', flags: ItemFlag::WEAPON, equipped: true);
        $inventory->add($item2);

        self::assertSame($item2, $inventory->getEquippedWeapon());
    }

    public function testConsumeItem(): void
    {
        $item = new Item('Health Potion', 'potion', flags: 1);
        $inventory = new EntityInventory(0);
        $inventory->add($item);

        $inventory->consume('Health Potion');

        self::assertNull($inventory->get('Health Potion'));
    }

    public function testConsumeItemButItemDoesNotExists(): void
    {
        $item = new Item('Health Potion', 'potion', flags: 1);
        $inventory = new EntityInventory(0);
        $inventory->add($item);

        self::expectException(InventoryException::class);
        self::expectExceptionCode(1700);
        $inventory->consume('Great Health Potion');
    }

    public function testSetQuantityOnItemButWithValueBelowZero(): void
    {
        $item = new Item('Health Potion', 'potion', flags: 1);

        self::expectException(ItemException::class);
        self::expectExceptionCode(1301);
        $item->setQuantity(-1);
    }

    public function testJsonSerialize(): void
    {
        $data = [
            'coins' => 10,
            'items' => [
                [
                    'type'        => 'item',
                    'name'        => 'The Sword #2',
                    'subType'     => 'sword',
                    'description' => '',
                    'modifiers'   => [],
                    'flags'       => ItemFlag::WEAPON,
                    'equipped'    => true,
                    'damages'     => 0,
                    'quantity'    => 1,
                    'price'       => 0,
                ],
            ],
        ];
        $item = new Item('The Sword #2', 'sword', flags: ItemFlag::WEAPON, equipped: true);

        $inventory = new EntityInventory(10);
        $inventory->add($item);

        self::assertSame($data, $inventory->jsonSerialize());
    }
}
