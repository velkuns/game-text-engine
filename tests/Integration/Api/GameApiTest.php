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
use Velkuns\GameTextEngine\Api\AbilitiesApi;
use Velkuns\GameTextEngine\Api\BestiaryApi;
use Velkuns\GameTextEngine\Api\CombatApi;
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
        $gameApi = new GameApi(
            new JsonLoader(),
            new DOTExporter(),
            new StoryApi(self::getGraphFactory()),
            self::getItemsApi(),
            self::getBestiaryApi(),
            self::getAbilitiesApi(),
            self::getStatusesApi(),
            self::getPlayerApi(),
            new CombatApi(new Randomizer(new Mt19937()), new TimeProcessor()),
        );

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
}
