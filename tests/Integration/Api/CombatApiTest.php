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
use Velkuns\GameTextEngine\Tests\Helper\ApiTrait;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;

class CombatApiTest extends TestCase
{
    use ApiTrait;
    use EntityTrait;

    public function testCombat1(): void
    {
        $combatApi = self::getCombatApi();

        $player = self::getPlayer();
        $goblin = self::getGoblin();

        self::assertSame(24, $player->getAbilities()->get('vitality')?->getValue());
        self::assertSame(16, $goblin->getAbilities()->get('vitality')?->getValue());

        //~ Turn #1 - Player attacks Goblin
        $log = $combatApi->tick($player, $goblin);

        self::assertTrue($log->isHit(), $log->debug['hitChance'] ?? '');
        self::assertSame(5, $log->damages, $log->debug['damages'] ?? '');
        self::assertSame(24, $player->getAbilities()->get('vitality')->getValue());
        self::assertSame(11, $goblin->getAbilities()->get('vitality')->getValue());
        self::assertSame("Brave Test Hero #1 'hit' Goblin #1 with The Sword and make 5 damage(s).", (string) $log);
        self::assertSame('((10 * 2) / 8 ) + 3', $log->debug['damages'] ?? '');

        //~ Turn #2 - Goblin attacks Player
        $log = $combatApi->tick($goblin, $player);

        self::assertTrue($log->isHit(), $log->debug['hitChance'] ?? '');
        self::assertSame(2, $log->damages, $log->debug['damages'] ?? '');
        self::assertSame(22, $player->getAbilities()->get('vitality')->getValue());
        self::assertSame(11, $goblin->getAbilities()->get('vitality')->getValue());
        self::assertSame("Goblin #1 'hit' Brave Test Hero #1 with The Dagger and make 2 damage(s).", (string) $log);
        self::assertSame('((8 * 2) / 14 ) + 1', $log->debug['damages'] ?? '');

        //~ Turn #3 - Player attacks Goblin
        $log = $combatApi->tick($player, $goblin);

        self::assertFalse($log->isHit(), $log->debug['hitChance'] ?? '');
        self::assertSame(5, $log->damages, $log->debug['damages'] ?? '');
        self::assertSame(22, $player->getAbilities()->get('vitality')->getValue());
        self::assertSame(11, $goblin->getAbilities()->get('vitality')->getValue());
        self::assertSame("Brave Test Hero #1 'miss' Goblin #1 with The Sword.", (string) $log);

        //~ Turn #4 - Goblin attacks Player
        $log = $combatApi->tick($goblin, $player);

        self::assertFalse($log->isHit(), $log->debug['hitChance'] ?? '');
        self::assertSame(2, $log->damages, $log->debug['damages'] ?? '');
        self::assertSame(22, $player->getAbilities()->get('vitality')->getValue());
        self::assertSame(11, $goblin->getAbilities()->get('vitality')->getValue());

        self::assertTrue($player->isAlive());
        self::assertTrue($goblin->isAlive());
    }

    public function testCombat2(): void
    {
        $combatApi = self::getCombatApi();

        $player  = self::getPlayer()->clone(); // Player already have rested with remaining turn = 1
        $rat1    = self::getBestiaryApi()->get('rat'); // get cloned rat
        $rat2    = self::getBestiaryApi()->get('rat'); // get cloned rat

        self::assertSame(1, $player->getStatuses()->getByType('state', 'Rested')?->getRemainingTurns());
        $combatApi->auto($player, [$rat1, $rat2]);
        self::assertNull($player->getStatuses()->getByType('state', 'Rested')); // Has been removed

        self::assertTrue($player->isAlive());
        self::assertFalse($rat1->isAlive());
        self::assertFalse($rat2->isAlive());
    }

    public function testLoot(): void
    {
        $combatApi = self::getCombatApi();

        $player = self::getPlayer();
        $enemy  = self::getBestiaryApi()->get('Chief Goblin');

        self::assertSame(100, $player->getInventory()->coins);
        self::assertNull($player->getInventory()->get('Small Health Potion'));
        self::assertNull($player->getInventory()->get('Iron Sword'));
        $log = $combatApi->loot($player, $enemy);
        self::assertSame(107, $player->getInventory()->coins);
        self::assertNotNull($player->getInventory()->get('Small Health Potion'));
        self::assertNotNull($player->getInventory()->get('Iron Sword'));

        self::assertSame('You found 7 coins and Small Health Potion, Iron Sword items on Chief Goblin.', (string) $log);
    }

    public function testXp(): void
    {
        $combatApi = self::getCombatApi();

        $player = self::getPlayer();
        $enemy  = self::getBestiaryApi()->get('Rat');

        self::assertSame(500, $player->getInfo()->xp);
        $log = $combatApi->xp($player, $enemy);
        self::assertSame(540, $player->getInfo()->xp); // xp: 10 (base) + 3 (lvl) * 10 (bonus / lvl)
        self::assertSame('You gain 40 XP from Rat kill.', (string) $log);

        $enemy  = self::getBestiaryApi()->get('Chief Goblin');
        self::assertSame(540, $player->getInfo()->xp);
        $log = $combatApi->xp($player, $enemy);
        self::assertSame(570, $player->getInfo()->xp); // xp: 30, defined in bestiary
        self::assertSame('You gain 30 XP from Chief Goblin kill.', (string) $log);
    }
}
