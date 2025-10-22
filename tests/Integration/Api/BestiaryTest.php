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
use Velkuns\GameTextEngine\Api\Bestiary;
use Velkuns\GameTextEngine\Api\Exception\BestiaryException;
use Velkuns\GameTextEngine\Api\Items;
use Velkuns\GameTextEngine\Element\Item\ItemInterface;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;
use Velkuns\GameTextEngine\Utils\Loader\JsonLoader;

/**
 * @phpstan-import-type ItemData from ItemInterface
 * @phpstan-import-type BestiaryData from Bestiary
 */
class BestiaryTest extends TestCase
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

        $bestiary = new Bestiary(self::getEntityFactory(), $items);

        /** @var list<BestiaryData> $bestiaryData */
        $bestiaryData = $loader->fromFile($dataDir . '/bestiary.json');
        $bestiary->load($bestiaryData);

        $entity = $bestiary->get('Goblin');

        self::assertSame('Goblin', $entity->getName());
        self::assertSame('creature', $entity->getType());
        self::assertEquals($items->get('Rusty Dagger'), $entity->getInventory()->get('Rusty Dagger'));
    }

    public function testLoadWhenThrowException(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../data');
        $loader  = new JsonLoader();
        $items   = new Items(self::getItemFactory());

        /** @var list<ItemData> $itemsData */
        $itemsData = $loader->fromFile($dataDir . '/items.json');
        $items->load($itemsData);

        $bestiary = new Bestiary(self::getEntityFactory(), $items);

        /** @var list<BestiaryData> $bestiaryData */
        $bestiaryData = $loader->fromFile($dataDir . '/bestiary.json');
        $bestiary->load($bestiaryData);

        self::expectException(BestiaryException::class);
        self::expectExceptionMessage("Entity 'King Goblin' not found in bestiary.");
        $entity = $bestiary->get('King Goblin');
    }
}
