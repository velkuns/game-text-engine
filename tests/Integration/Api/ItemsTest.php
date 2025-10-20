<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Integration\Api;

use Velkuns\GameTextEngine\Api\Bestiary;
use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Api\Exception\ItemException;
use Velkuns\GameTextEngine\Api\Items;
use Velkuns\GameTextEngine\Api\Loader\JsonLoader;
use Velkuns\GameTextEngine\Element\Item\ItemInterface;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;

/**
 * @phpstan-import-type ItemData from ItemInterface
 * @phpstan-import-type BestiaryData from Bestiary
 */
class ItemsTest extends TestCase
{
    use FactoryTrait;

    public function testLoad(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../data');
        $loader  = new JsonLoader();
        $items   = new Items(self::getItemFactory());

        /** @var list<ItemData> $itemsData */
        $itemsData = $loader->fromFile($dataDir . '/items.json');
        $items->load($itemsData);


        $item = $items->get('Sharp Iron Sword');

        self::assertSame('Sharp Iron Sword', $item->getName());
        self::assertSame('sword', $item->getSubType());
    }

    public function testLoadWhenThrowException(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../data');
        $loader  = new JsonLoader();
        $items   = new Items(self::getItemFactory());

        /** @var list<ItemData> $itemsData */
        $itemsData = $loader->fromFile($dataDir . '/items.json');
        $items->load($itemsData);

        self::expectException(ItemException::class);
        self::expectExceptionMessage("Item 'Diamond Sword' not found in item list.");
        $entity = $items->get('Diamond Sword');
    }
}
