<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Element\Entity;

use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Element\Modifier\Modifier;
use Velkuns\GameTextEngine\Element\Status\Status;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;

class EntityTest extends TestCase
{
    use EntityTrait;

    public function testEntity(): void
    {
        $player = self::getPlayer();

        $player->getInventory()->get('The Sword')?->equip();

        self::assertSame('Brave Test Hero #1', $player->getName());
        self::assertSame(24, $player->getAbilities()->get('vitality')?->getValue());
    }

    public function testEntityStatuses(): void
    {
        $player  = self::getPlayer();
        $gobelin = self::getGoblin();

        $player->getStatuses()->set(new Status('skill', 'test', '', []));
        $player->getStatuses()->set(new Status('state', 'test', '', []));
        $player->getStatuses()->set(new Status('blessing', 'test', '', []));
        $player->getStatuses()->set(new Status('curse', 'test', '', []));
        $player->getStatuses()->set(new Status('title', 'test', '', []));

        $expected  = [
            new Modifier('self.damages.physical.value', 1),
            new Modifier('self.abilities.vitality.value', 2),
        ];
        $modifiers = $player->getModifiers($gobelin);

        self::assertEquals($expected, $modifiers);
    }

    public function testJsonSerialize(): void
    {
        $data   = self::getPlayerData();
        $player = self::getPlayer();

        self::assertSame($data, $player->jsonSerialize());
    }
}
