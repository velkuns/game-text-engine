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
use Velkuns\GameTextEngine\Api\GameApi;
use Velkuns\GameTextEngine\Api\StoryApi;
use Velkuns\GameTextEngine\Core\Exporter\DOTExporter;
use Velkuns\GameTextEngine\Core\Loader\JsonLoader;
use Velkuns\GameTextEngine\Exception\Api\StoryApiException;
use Velkuns\GameTextEngine\Tests\Helper\ApiTrait;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;

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
            $dataDir . '/rules/rules_attributes.json',
            $dataDir . '/rules/rules_statuses.json',
            $dataDir . '/rules/rules_combat.json',
            $dataDir . '/rules/rules_player.json',
            $dataDir . '/templates/player.json',
        );

        $dump = $gameApi->dump(true);

        self::assertSame(\trim((string) \file_get_contents($dataDir . '/stories/test.json')), $dump['story']);
        self::assertSame(\trim((string) \file_get_contents($dataDir . '/items.json')), $dump['items']);
        self::assertSame(\trim((string) \file_get_contents($dataDir . '/bestiary.json')), $dump['bestiary']);
        self::assertSame(\trim((string) \file_get_contents($dataDir . '/rules/rules_attributes.json')), $dump['attributesRules']);
        self::assertSame(\trim((string) \file_get_contents($dataDir . '/rules/rules_statuses.json')), $dump['statusesRules']);
        self::assertSame(\trim((string) \file_get_contents($dataDir . '/rules/rules_combat.json')), $dump['combatRules']);
        self::assertSame(\trim((string) \file_get_contents($dataDir . '/rules/rules_player.json')), $dump['playerRules']);
        self::assertSame(\trim((string) \file_get_contents($dataDir . '/templates/player.json')), $dump['playerData']);
    }

    public function testLoadFromJson(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../data');

        $storyJson          = (string) \file_get_contents($dataDir . '/stories/test.json');
        $itemsJson          = (string) \file_get_contents($dataDir . '/items.json');
        $bestiaryJson       = (string) \file_get_contents($dataDir . '/bestiary.json');
        $attributesRulesJson = (string) \file_get_contents($dataDir . '/rules/rules_attributes.json');
        $statusesRulesJson  = (string) \file_get_contents($dataDir . '/rules/rules_statuses.json');
        $combatRulesJson    = (string) \file_get_contents($dataDir . '/rules/rules_combat.json');
        $playerRulesJson    = (string) \file_get_contents($dataDir . '/rules/rules_player.json');
        $playerDataJson     = (string) \file_get_contents($dataDir . '/templates/player.json');

        $gameApi = $this->getGameApi();
        $gameApi->loadFromJsons(
            $storyJson,
            $itemsJson,
            $bestiaryJson,
            $attributesRulesJson,
            $statusesRulesJson,
            $combatRulesJson,
            $playerRulesJson,
            $playerDataJson,
        );

        $dump = $gameApi->dump(true);

        self::assertSame(\trim($storyJson), $dump['story']);
        self::assertSame(\trim($itemsJson), $dump['items']);
        self::assertSame(\trim($bestiaryJson), $dump['bestiary']);
        self::assertSame(\trim($attributesRulesJson), $dump['attributesRules']);
        self::assertSame(\trim($statusesRulesJson), $dump['statusesRules']);
        self::assertSame(\trim($combatRulesJson), $dump['combatRules']);
        self::assertSame(\trim($playerRulesJson), $dump['playerRules']);
        self::assertSame(\trim($playerDataJson), $dump['playerData']);
    }

    public function testRead(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../data');

        $gameApi = $this->getGameApi();
        $gameApi->loadFromFiles(
            $dataDir . '/stories/test.json',
            $dataDir . '/items.json',
            $dataDir . '/bestiary.json',
            $dataDir . '/rules/rules_attributes.json',
            $dataDir . '/rules/rules_statuses.json',
            $dataDir . '/rules/rules_combat.json',
            $dataDir . '/rules/rules_player.json',
            $dataDir . '/templates/player.json',
        );

        //~ Test on next node
        [$node, $edges, $logs] = $gameApi->read('1', '2');

        $graph = $gameApi->story->graph;
        $expectedNode  = $graph->getNode('2');
        $expectedEdges = \array_values($graph->getEdgesFromSource('2'));

        self::assertSame($expectedNode, $node);
        self::assertSame($expectedEdges, $edges);
        self::assertSame(['combat' => [], 'loot' => [], 'xp' => []], $logs);
    }

    public function testReadOnSameNode(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../data');

        $gameApi = $this->getGameApi();
        $gameApi->loadFromFiles(
            $dataDir . '/stories/test.json',
            $dataDir . '/items.json',
            $dataDir . '/bestiary.json',
            $dataDir . '/rules/rules_attributes.json',
            $dataDir . '/rules/rules_statuses.json',
            $dataDir . '/rules/rules_combat.json',
            $dataDir . '/rules/rules_player.json',
            $dataDir . '/templates/player.json',
        );

        //~ Test on next node
        [$node, $edges, $logs] = $gameApi->read('1', '1');

        $graph = $gameApi->story->graph;
        $expectedNode  = $graph->getNode('1');
        $expectedEdges = \array_values($graph->getEdgesFromSource('1'));

        self::assertSame($expectedNode, $node);
        self::assertSame($expectedEdges, $edges);
        self::assertSame(['combat' => [], 'loot' => [], 'xp' => []], $logs);
    }

    public function testReadWithTrigger(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../data');

        $gameApi = $this->getGameApi();
        $gameApi->loadFromFiles(
            $dataDir . '/stories/test.json',
            $dataDir . '/items.json',
            $dataDir . '/bestiary.json',
            $dataDir . '/rules/rules_attributes.json',
            $dataDir . '/rules/rules_statuses.json',
            $dataDir . '/rules/rules_combat.json',
            $dataDir . '/rules/rules_player.json',
            $dataDir . '/templates/player.json',
        );

        //~ Test on next node
        [$node, $edges, $logs] = $gameApi->read('3', '4');

        $graph = $gameApi->story->graph;
        $expectedNode  = $graph->getNode('4');
        $expectedEdges = $graph->getEdgesFromSource('4');
        unset($expectedEdges['998']);

        self::assertSame($expectedNode, $node);
        self::assertSame(\array_values($expectedEdges), $edges);
        self::assertNotEmpty($logs['combat']);
        self::assertNotEmpty($logs['loot']);
        self::assertNotEmpty($logs['xp']);
    }

    public function testReadButIsSameNode(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../data');

        $gameApi = $this->getGameApi();
        $gameApi->loadFromFiles(
            $dataDir . '/stories/test.json',
            $dataDir . '/items.json',
            $dataDir . '/bestiary.json',
            $dataDir . '/rules/rules_attributes.json',
            $dataDir . '/rules/rules_statuses.json',
            $dataDir . '/rules/rules_combat.json',
            $dataDir . '/rules/rules_player.json',
            $dataDir . '/templates/player.json',
        );

        //~ Test on next node
        [$node, $edges, $logs] = $gameApi->read('1', '1');

        $graph = $gameApi->story->graph;
        $expectedNode  = $graph->getNode('1');
        $expectedEdges = \array_values($graph->getEdgesFromSource('1'));

        self::assertSame($expectedNode, $node);
        self::assertSame($expectedEdges, $edges);
        self::assertSame(['combat' => [], 'loot' => [], 'xp' => []], $logs);
    }

    public function testReadButTargetIsNotValid(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../data');

        $gameApi = $this->getGameApi();
        $gameApi->loadFromFiles(
            $dataDir . '/stories/test.json',
            $dataDir . '/items.json',
            $dataDir . '/bestiary.json',
            $dataDir . '/rules/rules_attributes.json',
            $dataDir . '/rules/rules_statuses.json',
            $dataDir . '/rules/rules_combat.json',
            $dataDir . '/rules/rules_player.json',
            $dataDir . '/templates/player.json',
        );

        self::expectException(StoryApiException::class);
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
            self::getAttributesApi(),
            self::getStatusesApi(),
            self::getPlayerApi(),
            self::getCombatApi(),
        );
    }
}
