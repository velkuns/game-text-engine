<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Integration\Api;

use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Exception\Api\ItemApiException;
use Velkuns\GameTextEngine\Tests\Helper\ApiTrait;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;

class ItemsApiTest extends TestCase
{
    use ApiTrait;
    use FactoryTrait;

    public function testLoad(): void
    {
        $items = self::getItemsApi();

        $item = $items->get('Sharp Iron Sword');

        self::assertSame('Sharp Iron Sword', $item->getName());
        self::assertSame('sword', $item->getSubType());
    }

    public function testAndAndRemoveItem(): void
    {
        $items = self::getItemsApi();

        $staff = self::getItemFactory()->from([
            'name'        => 'Staff',
            'type'        => 'weapon',
            'subType'     => 'staff',
            'description' => 'A basic wooden staff.',
            'modifiers'   => [],
            'flags'       => 6,
            'equipped'    => false,
            'damages'     => ['physical' => ['type' => 'physical', 'value' => 2]],
            'price'       => 0,
        ]);

        $items->set($staff);
        self::assertSame($staff, $items->get('Staff', false));

        //~ Remove item
        $items->remove($staff->getName());

        self::expectException(ItemApiException::class);
        self::expectExceptionCode(1601);

        $items->get($staff->getName());
    }

    public function testLoadWhenThrowException(): void
    {
        $items = self::getItemsApi();

        self::expectException(ItemApiException::class);
        self::expectExceptionMessage("Item 'Diamond Sword' not found in item list.");
        $items->get('Diamond Sword');
    }
}
