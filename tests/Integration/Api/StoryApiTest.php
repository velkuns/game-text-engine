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
use Velkuns\GameTextEngine\Api\Exception\StoryException;
use Velkuns\GameTextEngine\Api\StoryApi;
use Velkuns\GameTextEngine\Graph\Graph;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;
use Velkuns\GameTextEngine\Utils\Loader\JsonLoader;

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

        self::assertSame('text_1', $node->id);
        self::assertSame('text_2', $story->getText('text_2')->id);
    }

    public function testGoto(): void
    {
        $story = $this->getStory();

        $player = self::getPlayer();

        $node = $story->goto('text_1', 'text_2', $player);

        self::assertSame('text_2', $node->id);
    }

    public function testGotoButWithNonPossibleChoice(): void
    {
        $story = $this->getStory();

        $player = self::getPlayer();
        $goblin = self::getGoblin();

        //~ Player is dead
        $player->getAbilities()->get('vitality')?->decrease(100);

        //~ Trying to go to text_998 (player dead)
        $node = $story->goto('text_4', 'text_998', $player, $goblin);
        self::assertSame('text_998', $node->id);

        self::expectException(StoryException::class);
        self::expectExceptionCode(1400);

        //~ Trying to go to text_997 (player win)
        $story->goto('text_4', 'text_997', $player);
    }

    public function testGetPossibleChoices(): void
    {
        $story = $this->getStory();

        $player = self::getPlayer();
        $goblin = self::getGoblin();

        //~ Both alive, should have no choices
        $edges = $story->getPossibleChoices('text_4', $player, $goblin);
        self::assertCount(1, $edges);

        //~ Combat simulation, Player wins
        $goblin->getAbilities()->get('vitality')?->decrease(100);

        $edges = $story->getPossibleChoices('text_4', $player, $goblin);
        self::assertCount(1, $edges);
        self::assertSame('Vous avez vaincu!', $edges[0]->content);

        //~ Combat simulation, Player dies
        $player = self::getPlayer();
        $goblin = self::getGoblin();

        $player->getAbilities()->get('vitality')?->decrease(100);

        $edges = $story->getPossibleChoices('text_4', $player, $goblin);
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
