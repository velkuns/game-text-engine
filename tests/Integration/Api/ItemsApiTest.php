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
use Velkuns\GameTextEngine\Api\BestiaryApi;
use Velkuns\GameTextEngine\Api\Exception\ItemException;
use Velkuns\GameTextEngine\Api\ItemsApi;
use Velkuns\GameTextEngine\Element\Item\ItemInterface;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;
use Velkuns\GameTextEngine\Utils\Loader\JsonLoader;

/**
 * @phpstan-import-type ItemData from ItemInterface
 * @phpstan-import-type BestiaryData from BestiaryApi
 */
class ItemsApiTest extends TestCase
{
    use FactoryTrait;

    public function testLoad(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../data');
        $loader  = new JsonLoader();
        $items   = new ItemsApi(self::getItemFactory());

        /** @var list<ItemData> $itemsData */
        $itemsData = $loader->fromFile($dataDir . '/items.json');
        $items->load($itemsData);


        $item = $items->get('Sharp Iron Sword');

        self::assertSame('Sharp Iron Sword', $item->getName());
        self::assertSame('sword', $item->getSubType());
    }

    public function testAndAndRemoveItem(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../data');
        $loader  = new JsonLoader();
        $items   = new ItemsApi(self::getItemFactory());

        /** @var list<ItemData> $itemsData */
        $itemsData = $loader->fromFile($dataDir . '/items.json');
        $items->load($itemsData);

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

        self::expectException(ItemException::class);
        self::expectExceptionCode(1601);

        $items->get($staff->getName());
    }

    public function testLoadWhenThrowException(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../data');
        $loader  = new JsonLoader();
        $items   = new ItemsApi(self::getItemFactory());

        /** @var list<ItemData> $itemsData */
        $itemsData = $loader->fromFile($dataDir . '/items.json');
        $items->load($itemsData);

        self::expectException(ItemException::class);
        self::expectExceptionMessage("Item 'Diamond Sword' not found in item list.");
        $entity = $items->get('Diamond Sword');
    }
}
