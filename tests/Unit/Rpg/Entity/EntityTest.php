<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Rpg\Entity;

use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;

class EntityTest extends TestCase
{
    use EntityTrait;
    use FactoryTrait;

    public function testEntity(): void
    {
        $player = self::getPlayer();

        $player->getInventory()->get('The Sword')?->equip();

        self::assertSame('Brave Test Hero #1', $player->getName());
        self::assertSame(24, $player->getAttributes()->get('vitality')?->getValue());
    }

    public function testEntityTraits(): void
    {
        $player  = self::getPlayer();
        $gobelin = self::getGoblin();

        $conditions = self::getPrerequisitesFactory()->from(
            [
                'numberRequired' => 1,
                'requirements'   => [
                    [
                        'type'      => 'enemy.info',
                        'condition' => 'race=goblin',
                        'is'        => true,
                    ],
                ],
            ],
        );

        //~ Be sure the sword is unequipped before getting the modifiers
        $player->getInventory()->get('The Sword')?->unequip();

        $expected  = [
            new Modifier('self.damages.physical.value', 1, $conditions),
            new Modifier('self.attribute.vitality.value', 2),
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
