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
use Velkuns\GameTextEngine\Api\Combat;
use Velkuns\GameTextEngine\Element\Processor\TimeProcessor;
use Velkuns\GameTextEngine\Tests\Helper\ApiTrait;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;

class CombatTest extends TestCase
{
    use ApiTrait;
    use EntityTrait;

    private Combat $combat;

    private const int SEED = 42; // For reproducible tests

    public function setUp(): void
    {
        $this->combat = new Combat(new Randomizer(new Mt19937(self::SEED)), new TimeProcessor());
    }

    public function testCombat1(): void
    {
        $player = self::getPlayer();
        $goblin = self::getGoblin();

        self::assertSame(24, $player->getAbilities()->get('vitality')?->getValue());
        self::assertSame(16, $goblin->getAbilities()->get('vitality')?->getValue());

        //~ Turn #1 - Player attacks Goblin
        $log = $this->combat->tick($player, $goblin);

        self::assertFalse($log->isHit(), $log->debug['hitChance'] ?? '');
        self::assertSame(6, $log->damages, $log->debug['damages'] ?? '');
        self::assertSame(24, $player->getAbilities()->get('vitality')->getValue());
        self::assertSame(16, $goblin->getAbilities()->get('vitality')->getValue());
        self::assertSame("Brave Test Hero #1 'miss' Goblin #1 with The Sword.", (string) $log);
        self::assertSame('damages = ((10 * 2 ) / 8) + 3 = 2.5 + 3 = 6', $log->debug['damages'] ?? '');

        //~ Turn #2 - Goblin attacks Player
        $log = $this->combat->tick($goblin, $player);

        self::assertTrue($log->isHit(), $log->debug['hitChance'] ?? '');
        self::assertSame(2, $log->damages, $log->debug['damages'] ?? '');
        self::assertSame(22, $player->getAbilities()->get('vitality')->getValue());
        self::assertSame(16, $goblin->getAbilities()->get('vitality')->getValue());
        self::assertSame("Goblin #1 'hit' Brave Test Hero #1 with The Dagger and make 2 damage(s).", (string) $log);
        self::assertSame('damages = ((8 * 2 ) / 14) + 1 = 1.1428571428571 + 1 = 2', $log->debug['damages'] ?? '');

        //~ Turn #3 - Player attacks Goblin
        $log = $this->combat->tick($player, $goblin);

        self::assertFalse($log->isHit(), $log->debug['hitChance'] ?? '');
        self::assertSame(6, $log->damages, $log->debug['damages'] ?? '');
        self::assertSame(22, $player->getAbilities()->get('vitality')->getValue());
        self::assertSame(16, $goblin->getAbilities()->get('vitality')->getValue());

        //~ Turn #4 - Goblin attacks Player
        $log = $this->combat->tick($goblin, $player);

        self::assertFalse($log->isHit(), $log->debug['hitChance'] ?? '');
        self::assertSame(2, $log->damages, $log->debug['damages'] ?? '');
        self::assertSame(22, $player->getAbilities()->get('vitality')->getValue());
        self::assertSame(16, $goblin->getAbilities()->get('vitality')->getValue());

        self::assertTrue($player->isAlive());
        self::assertTrue($goblin->isAlive());
    }

    public function testCombat2(): void
    {
        $player  = self::getPlayer()->clone(); // Player already have rested with remaining turn = 1
        $rat1    = self::getBestiary()->get('rat'); // get cloned rat
        $rat2    = self::getBestiary()->get('rat'); // get cloned rat

        self::assertSame(1, $player->getStatuses()->states['Rested']->getRemainingTurns());
        $this->combat->start($player, [$rat1, $rat2]);
        self::assertSame(0, $player->getStatuses()->states['Rested']->getRemainingTurns());

        self::assertTrue($player->isAlive());
        self::assertFalse($rat1->isAlive());
        self::assertFalse($rat2->isAlive());
    }
}
