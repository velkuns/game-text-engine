<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Action;

use Random\Engine\Mt19937;
use Random\Randomizer;
use Velkuns\GameTextEngine\Action\Combat;
use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Tests\Unit\Helper\EntityTrait;

class CombatTest extends TestCase
{
    use EntityTrait;

    private Combat $combat;

    private const int SEED = 42; // For reproducible tests

    public function setUp(): void
    {
        $this->combat = new Combat(new Randomizer(new Mt19937(self::SEED)));
    }

    public function testCombat1(): void
    {
        $player = $this->getPlayer();
        $goblin = $this->getGoblin();

        self::assertSame(14, $player->getAbilities()->get('vitality')?->getCurrent());
        self::assertSame(8, $goblin->getAbilities()->get('vitality')?->getCurrent());

        //~ Turn #1 - Player attacks Goblin
        echo "Turn #1 - Player attacks Goblin\n";
        $result = $this->combat->turn($player, $goblin);
        $chance = round($result['chance'] * 100) . '%';
        $roll   = round($result['roll'] * 100) . '%';
        echo " > Player 'hit' and inflicts " . $result['damages'] . " damages to Goblin (hit chance: $chance, roll: $roll)\n\n";

        self::assertTrue($result['hit'], $result['debug']['hit chance']);
        self::assertSame(5, $result['damages'], $result['debug']['damages']);
        self::assertSame(14, $player->getAbilities()->get('vitality')->getCurrent());
        self::assertSame(3, $goblin->getAbilities()->get('vitality')->getCurrent());

        //~ Turn #2 - Goblin attacks Player
        echo "Turn #2 - Goblin attacks Player\n";
        $result = $this->combat->turn($goblin, $player);
        $chance = round($result['chance'] * 100) . '%';
        $roll   = round($result['roll'] * 100) . '%';
        echo " > Goblin 'hit' and inflicts " . $result['damages'] . " damages to Player (hit chance: $chance, roll: $roll)\n\n";

        self::assertTrue($result['hit'], $result['debug']['hit chance']);
        self::assertSame(2, $result['damages'], $result['debug']['damages']);
        self::assertSame(12, $player->getAbilities()->get('vitality')->getCurrent());
        self::assertSame(3, $goblin->getAbilities()->get('vitality')->getCurrent());

        //~ Turn #3 - Player attacks Goblin
        echo "Turn #3 - Player attacks Goblin\n";
        $result = $this->combat->turn($player, $goblin);
        $chance = round($result['chance'] * 100) . '%';
        $roll   = round($result['roll'] * 100) . '%';
        echo " > Player 'hit' and inflicts " . $result['damages'] . " damages to Goblin (hit chance: $chance, roll: $roll)\n";
        echo " > Goblin is dead!\n\n";

        self::assertTrue($result['hit'], $result['debug']['hit chance']);
        self::assertSame(5, $result['damages'], $result['debug']['damages']);
        self::assertSame(12, $player->getAbilities()->get('vitality')->getCurrent());
        self::assertSame(0, $goblin->getAbilities()->get('vitality')->getCurrent());

        //~ Turn #4 - Dead Goblin attacks Player
        echo "Turn #4 - Dead Goblin attacks Player\n";
        $result = $this->combat->turn($goblin, $player);
        $chance = round($result['chance'] * 100) . '%';
        $roll   = round($result['roll'] * 100) . '%';
        echo " > Goblin 'miss' Player (hit chance: $chance, roll: $roll)\n\n";

        self::assertFalse($result['hit'], $result['debug']['hit chance']);
        self::assertSame(2, $result['damages'], $result['debug']['damages']);
        self::assertSame(12, $player->getAbilities()->get('vitality')->getCurrent());
        self::assertSame(0, $goblin->getAbilities()->get('vitality')->getCurrent());
    }
}
