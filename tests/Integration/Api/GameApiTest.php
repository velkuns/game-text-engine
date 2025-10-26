<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Api;

use PHPUnit\Framework\TestCase;
use Random\Engine\Mt19937;
use Random\Randomizer;
use Velkuns\GameTextEngine\Api\Bestiary;
use Velkuns\GameTextEngine\Api\Combat;
use Velkuns\GameTextEngine\Api\GameApi;
use Velkuns\GameTextEngine\Api\Items;
use Velkuns\GameTextEngine\Api\Player;
use Velkuns\GameTextEngine\Api\Story;
use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Item\ItemInterface;
use Velkuns\GameTextEngine\Element\Modifier\ModifierProcessor;
use Velkuns\GameTextEngine\Element\Processor\TimeProcessor;
use Velkuns\GameTextEngine\Element\Resolver\TypeElementResolver;
use Velkuns\GameTextEngine\Graph\Graph;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;
use Velkuns\GameTextEngine\Utils\Loader\JsonLoader;

/**
 * @phpstan-import-type GraphData from Graph
 * @phpstan-import-type ItemData from ItemInterface
 * @phpstan-import-type BestiaryData from Bestiary
 * @phpstan-import-type EntityData from EntityInterface
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
            new Player(self::getEntityFactory(), $items, new ModifierProcessor(new TypeElementResolver())),
            new Combat(new Randomizer(new Mt19937()), new TimeProcessor()),
        );

        /** @var GraphData $storyData */
        $storyData    = $game->loader->fromFile($dataDir . '/stories/test.json');
        /** @var list<ItemData> $itemsData */
        $itemsData    = $game->loader->fromFile($dataDir . '/items.json');
        /** @var list<BestiaryData> $bestiaryData */
        $bestiaryData = $game->loader->fromFile($dataDir . '/bestiary.json');
        /** @var EntityData $playerData */
        $playerData = $game->loader->fromFile($dataDir . '/templates/player.json');

        $game->load($storyData, $itemsData, $bestiaryData, $playerData);

        $dump = $game->dump(true);

        self::assertSame(\trim((string) \file_get_contents($dataDir . '/stories/test.json')), $dump['story']);
        self::assertSame(\trim((string) \file_get_contents($dataDir . '/items.json')), $dump['items']);
        self::assertSame(\trim((string) \file_get_contents($dataDir . '/bestiary.json')), $dump['bestiary']);
        self::assertSame(\trim((string) \file_get_contents($dataDir . '/templates/player.json')), $dump['player']);
    }
}
