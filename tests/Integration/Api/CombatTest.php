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
use Velkuns\GameTextEngine\Api\Bestiary;
use Velkuns\GameTextEngine\Api\Combat;
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
        $this->combat = new Combat(new Randomizer(new Mt19937(self::SEED)));
    }

    public function testCombat1(): void
    {
        $player = self::getPlayer();
        $goblin = self::getGoblin();

        self::assertSame(24, $player->getAbilities()->get('vitality')?->getValue());
        self::assertSame(16, $goblin->getAbilities()->get('vitality')?->getValue());

        //~ Turn #1 - Player attacks Goblin
        //echo "Turn #1 - Player attacks Goblin\n";
        $result = $this->combat->turn($player, $goblin);
        $chance = round($result['chance'] * 100) . '%';
        $roll   = round($result['roll'] * 100);
        //echo " > Player 'miss' Goblin (hit chance: $chance, roll: $roll)\n\n";

        self::assertFalse($result['hit'], var_export($result, true));
        self::assertSame(5, $result['damages'], $result['debug']['damages']);
        self::assertSame(24, $player->getAbilities()->get('vitality')->getValue());
        self::assertSame(16, $goblin->getAbilities()->get('vitality')->getValue());

        //~ Turn #2 - Goblin attacks Player
        //echo "Turn #2 - Goblin attacks Player\n";
        $result = $this->combat->turn($goblin, $player);
        $chance = round($result['chance'] * 100) . '%';
        $roll   = round($result['roll'] * 100);
        //echo " > Goblin 'hit' and inflicts " . $result['damages'] . " damages to Player (hit chance: $chance, roll: $roll)\n\n";

        self::assertTrue($result['hit'], $result['debug']['hit chance']);
        self::assertSame(2, $result['damages'], $result['debug']['damages']);
        self::assertSame(22, $player->getAbilities()->get('vitality')->getValue());
        self::assertSame(16, $goblin->getAbilities()->get('vitality')->getValue());

        //~ Turn #3 - Player attacks Goblin
        //echo "Turn #3 - Player attacks Goblin\n";
        $result = $this->combat->turn($player, $goblin);
        $chance = round($result['chance'] * 100) . '%';
        $roll   = round($result['roll'] * 100);
        //echo " > Player 'miss' Goblin (hit chance: $chance, roll: $roll)\n\n";

        self::assertFalse($result['hit'], $result['debug']['hit chance']);
        self::assertSame(5, $result['damages'], $result['debug']['damages']);
        self::assertSame(22, $player->getAbilities()->get('vitality')->getValue());
        self::assertSame(16, $goblin->getAbilities()->get('vitality')->getValue());

        //~ Turn #4 - Goblin attacks Player
        //echo "Turn #4 - Goblin attacks Player\n";
        $result = $this->combat->turn($goblin, $player);
        $chance = round($result['chance'] * 100) . '%';
        $roll   = round($result['roll'] * 100);
        //echo " > Goblin 'miss' Player (hit chance: $chance, roll: $roll)\n\n";

        self::assertFalse($result['hit'], $result['debug']['hit chance']);
        self::assertSame(2, $result['damages'], $result['debug']['damages']);
        self::assertSame(22, $player->getAbilities()->get('vitality')->getValue());
        self::assertSame(16, $goblin->getAbilities()->get('vitality')->getValue());

        self::assertTrue($player->isAlive());
        self::assertTrue($goblin->isAlive());
    }

    public function testCombat2(): void
    {
        $player  = self::getPlayer()->clone();
        $rat1    = self::getBestiary()->get('rat'); // get cloned rat
        $rat2    = self::getBestiary()->get('rat'); // get cloned rat

        $this->combat->start($player, [$rat1, $rat2]);

        self::assertTrue($player->isAlive());
        self::assertFalse($rat1->isAlive());
        self::assertFalse($rat2->isAlive());
    }
}
