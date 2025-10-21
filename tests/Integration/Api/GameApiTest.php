<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Api;

use Random\Engine\Mt19937;
use Random\Randomizer;
use Velkuns\GameTextEngine\Api\Bestiary;
use Velkuns\GameTextEngine\Api\Combat;
use Velkuns\GameTextEngine\Api\GameApi;
use Velkuns\GameTextEngine\Api\Items;
use Velkuns\GameTextEngine\Api\Loader\JsonLoader;
use Velkuns\GameTextEngine\Api\Story;
use Velkuns\GameTextEngine\Element\Item\ItemInterface;
use Velkuns\GameTextEngine\Graph\Graph;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @phpstan-import-type GraphData from Graph
 * @phpstan-import-type ItemData from ItemInterface
 * @phpstan-import-type BestiaryData from Bestiary
 */
class GameApiTest extends TestCase
{
    use EntityTrait;
    use FactoryTrait;

    public function testLoad(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../data');
        $items = new Items(self::getItemFactory());
        $game = new GameApi(
            new JsonLoader(),
            new Story(self::getGraphFactory()),
            $items,
            new Bestiary(self::getEntityFactory(), $items),
            new Combat(new Randomizer(new Mt19937())),
        );

        /** @var GraphData $storyData */
        $storyData    = $game->loader->fromFile($dataDir . '/stories/test.json');
        /** @var list<ItemData> $itemsData */
        $itemsData    = $game->loader->fromFile($dataDir . '/items.json');
        /** @var list<BestiaryData> $bestiaryData */
        $bestiaryData = $game->loader->fromFile($dataDir . '/bestiary.json');

        $game->load($storyData, $itemsData, $bestiaryData);

        $dump = $game->dump(true);

        self::assertSame(\trim((string) \file_get_contents($dataDir . '/stories/test.json')), $dump['story']);
        self::assertSame(\trim((string) \file_get_contents($dataDir . '/items.json')), $dump['items']);
        self::assertSame(\trim((string) \file_get_contents($dataDir . '/bestiary.json')), $dump['bestiary']);
    }
}
