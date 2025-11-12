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
use Velkuns\GameTextEngine\Api\StoryApi;
use Velkuns\GameTextEngine\Core\Loader\JsonLoader;
use Velkuns\GameTextEngine\Exception\Api\StoryApiException;
use Velkuns\GameTextEngine\Graph\Graph;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;

/**
 * @phpstan-import-type GraphData from Graph
 */
class StoryApiTest extends TestCase
{
    use EntityTrait;
    use FactoryTrait;

    public function testLoad(): void
    {
        $story = $this->getStory();

        $node = $story->start();

        self::assertSame('1', $node->id);
        self::assertSame('2', $story->getText('2')->id);
    }

    public function testGoto(): void
    {
        $story = $this->getStory();

        $player = self::getPlayer();

        $node = $story->goto('1', '2', $player);

        self::assertSame('2', $node->id);
    }

    public function testGotoButWithNonPossibleChoice(): void
    {
        $story = $this->getStory();

        $player = self::getPlayer();
        $goblin = self::getGoblin();

        //~ Player is dead
        $player->getAttributes()->get('vitality')?->decrease(100);

        //~ Trying to go to 998 (player dead)
        $node = $story->goto('4', '998', $player, $goblin);
        self::assertSame('998', $node->id);

        self::expectException(StoryApiException::class);
        self::expectExceptionCode(1400);

        //~ Trying to go to 997 (player win)
        $story->goto('4', '997', $player);
    }

    public function testGetPossibleChoices(): void
    {
        $story = $this->getStory();

        $player = self::getPlayer();
        $goblin = self::getGoblin();

        //~ Both alive, should have no choices
        $edges = $story->getPossibleChoices('4', $player, $goblin);
        self::assertCount(1, $edges);

        //~ Combat simulation, Player wins
        $goblin->getAttributes()->get('vitality')?->decrease(100);

        $edges = $story->getPossibleChoices('4', $player, $goblin);
        self::assertCount(1, $edges);
        self::assertSame('Vous avez vaincu!', $edges[0]->content);

        //~ Combat simulation, Player dies
        $player = self::getPlayer();
        $goblin = self::getGoblin();

        $player->getAttributes()->get('vitality')?->decrease(100);

        $edges = $story->getPossibleChoices('4', $player, $goblin);
        self::assertCount(1, $edges);
        self::assertSame('Vous êtes mort...', $edges[0]->content);
    }

    private function getStory(): StoryApi
    {
        $dataDir = __DIR__ . '/../../../data';
        $loader = new JsonLoader();

        /** @var GraphData $data */
        $data = $loader->fromFile($dataDir . '/stories/test.json');

        $story = new StoryApi(self::getGraphFactory());
        $story->load($data);

        return $story;
    }
}
