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
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;

class EntityTest extends TestCase
{
    use EntityTrait;

    public function testEntity(): void
    {
        $player = self::getPlayer();

        self::assertSame('Brave Test Hero #1', $player->getName());
        self::assertSame(24, $player->getAbilities()->get('vitality')?->getValue());
    }

    public function testEntityStatuses(): void
    {
        $player  = self::getPlayer();
        $gobelin = self::getGoblin();

        $expected  = [
            new Modifier('ability.vitality', 2),
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
