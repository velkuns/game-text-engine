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
use Random\Engine\Mt19937;
use Random\Randomizer;
use Velkuns\GameTextEngine\Api\AbilitiesApi;
use Velkuns\GameTextEngine\Api\BestiaryApi;
use Velkuns\GameTextEngine\Api\CombatApi;
use Velkuns\GameTextEngine\Api\Exception\StoryException;
use Velkuns\GameTextEngine\Api\GameApi;
use Velkuns\GameTextEngine\Api\StatusesApi;
use Velkuns\GameTextEngine\Api\StoryApi;
use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Item\ItemInterface;
use Velkuns\GameTextEngine\Element\Processor\TimeProcessor;
use Velkuns\GameTextEngine\Graph\Graph;
use Velkuns\GameTextEngine\Tests\Helper\ApiTrait;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;
use Velkuns\GameTextEngine\Utils\Exporter\DOTExporter;
use Velkuns\GameTextEngine\Utils\Loader\JsonLoader;

/**
 * @phpstan-import-type GraphData from Graph
 * @phpstan-import-type ItemData from ItemInterface
 * @phpstan-import-type BestiaryData from BestiaryApi
 * @phpstan-import-type EntityData from EntityInterface
 * @phpstan-import-type AbilitiesRulesData from AbilitiesApi
 * @phpstan-import-type StatusesRulesData from StatusesApi
 */
class GameApiTest extends TestCase
{
    use ApiTrait;
    use EntityTrait;
    use FactoryTrait;

    public function testLoad(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../data');

        $gameApi = $this->getGameApi();
        $gameApi->loadFromFiles(
            $dataDir . '/stories/test.json',
            $dataDir . '/items.json',
            $dataDir . '/bestiary.json',
            $dataDir . '/rules/rules_abilities.json',
            $dataDir . '/rules/rules_statuses.json',
            $dataDir . '/templates/player.json',
        );

        $dump = $gameApi->dump(true);

        self::assertSame(\trim((string) \file_get_contents($dataDir . '/stories/test.json')), $dump['story']);
        self::assertSame(\trim((string) \file_get_contents($dataDir . '/items.json')), $dump['items']);
        self::assertSame(\trim((string) \file_get_contents($dataDir . '/bestiary.json')), $dump['bestiary']);
        self::assertSame(\trim((string) \file_get_contents($dataDir . '/rules/rules_abilities.json')), $dump['abilities']);
        self::assertSame(\trim((string) \file_get_contents($dataDir . '/rules/rules_statuses.json')), $dump['statuses']);
        self::assertSame(\trim((string) \file_get_contents($dataDir . '/templates/player.json')), $dump['player']);
    }

    public function testLoadFromJson(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../data');

        $storyJson     = (string) \file_get_contents($dataDir . '/stories/test.json');
        $itemsJson     = (string) \file_get_contents($dataDir . '/items.json');
        $bestiaryJson  = (string) \file_get_contents($dataDir . '/bestiary.json');
        $abilitiesJson = (string) \file_get_contents($dataDir . '/rules/rules_abilities.json');
        $statusesJson  = (string) \file_get_contents($dataDir . '/rules/rules_statuses.json');
        $playerJson    = (string) \file_get_contents($dataDir . '/templates/player.json');

        $gameApi = $this->getGameApi();
        $gameApi->loadFromJsons(
            $storyJson,
            $itemsJson,
            $bestiaryJson,
            $abilitiesJson,
            $statusesJson,
            $playerJson,
        );

        $dump = $gameApi->dump(true);

        self::assertSame(\trim($storyJson), $dump['story']);
        self::assertSame(\trim($itemsJson), $dump['items']);
        self::assertSame(\trim($bestiaryJson), $dump['bestiary']);
        self::assertSame(\trim($abilitiesJson), $dump['abilities']);
        self::assertSame(\trim($statusesJson), $dump['statuses']);
        self::assertSame(\trim($playerJson), $dump['player']);
    }

    public function testRead(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../data');

        $gameApi = $this->getGameApi();
        $gameApi->loadFromFiles(
            $dataDir . '/stories/test.json',
            $dataDir . '/items.json',
            $dataDir . '/bestiary.json',
            $dataDir . '/rules/rules_abilities.json',
            $dataDir . '/rules/rules_statuses.json',
            $dataDir . '/templates/player.json',
        );

        //~ Test on next node
        [$node, $edges, $logs] = $gameApi->read('1', '2');

        $graph = $gameApi->story->graph;
        $expectedNode  = $graph->getNode('2');
        $expectedEdges = \array_values($graph->getEdgesFromSource('2'));

        self::assertSame($expectedNode, $node);
        self::assertSame($expectedEdges, $edges);
        self::assertSame([], $logs);
    }

    public function testReadOnSameNode(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../data');

        $gameApi = $this->getGameApi();
        $gameApi->loadFromFiles(
            $dataDir . '/stories/test.json',
            $dataDir . '/items.json',
            $dataDir . '/bestiary.json',
            $dataDir . '/rules/rules_abilities.json',
            $dataDir . '/rules/rules_statuses.json',
            $dataDir . '/templates/player.json',
        );

        //~ Test on next node
        [$node, $edges, $logs] = $gameApi->read('1', '1');

        $graph = $gameApi->story->graph;
        $expectedNode  = $graph->getNode('1');
        $expectedEdges = \array_values($graph->getEdgesFromSource('1'));

        self::assertSame($expectedNode, $node);
        self::assertSame($expectedEdges, $edges);
        self::assertSame([], $logs);
    }

    public function testReadWithTrigger(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../data');

        $gameApi = $this->getGameApi();
        $gameApi->loadFromFiles(
            $dataDir . '/stories/test.json',
            $dataDir . '/items.json',
            $dataDir . '/bestiary.json',
            $dataDir . '/rules/rules_abilities.json',
            $dataDir . '/rules/rules_statuses.json',
            $dataDir . '/templates/player.json',
        );

        //~ Test on next node
        [$node, $edges, $logs] = $gameApi->read('3', '4');

        $graph = $gameApi->story->graph;
        $expectedNode  = $graph->getNode('4');
        $expectedEdges = $graph->getEdgesFromSource('4');
        unset($expectedEdges['997']);

        self::assertSame($expectedNode, $node);
        self::assertSame(\array_values($expectedEdges), $edges);
        self::assertNotEmpty($logs);
    }

    public function testReadButIsSameNode(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../data');

        $gameApi = $this->getGameApi();
        $gameApi->loadFromFiles(
            $dataDir . '/stories/test.json',
            $dataDir . '/items.json',
            $dataDir . '/bestiary.json',
            $dataDir . '/rules/rules_abilities.json',
            $dataDir . '/rules/rules_statuses.json',
            $dataDir . '/templates/player.json',
        );

        //~ Test on next node
        [$node, $edges, $logs] = $gameApi->read('1', '1');

        $graph = $gameApi->story->graph;
        $expectedNode  = $graph->getNode('1');
        $expectedEdges = \array_values($graph->getEdgesFromSource('1'));

        self::assertSame($expectedNode, $node);
        self::assertSame($expectedEdges, $edges);
        self::assertSame([], $logs);
    }

    public function testReadButTargetIsNotValid(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../data');

        $gameApi = $this->getGameApi();
        $gameApi->loadFromFiles(
            $dataDir . '/stories/test.json',
            $dataDir . '/items.json',
            $dataDir . '/bestiary.json',
            $dataDir . '/rules/rules_abilities.json',
            $dataDir . '/rules/rules_statuses.json',
            $dataDir . '/templates/player.json',
        );

        self::expectException(StoryException::class);
        self::expectExceptionCode(1400);
        $gameApi->read('1', '3');
    }

    private function getGameApi(): GameApi
    {
        return new GameApi(
            new JsonLoader(),
            new DOTExporter(),
            new StoryApi(self::getGraphFactory()),
            self::getItemsApi(),
            self::getBestiaryApi(),
            self::getAbilitiesApi(),
            self::getStatusesApi(),
            self::getPlayerApi(),
            new CombatApi(new Randomizer(new Mt19937(42)), new TimeProcessor()),
        );
    }
}
