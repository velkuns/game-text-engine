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
        self::assertSame(24, $player->getAbilities()->get('vitality')?->getValue());
    }

    public function testEntityStatuses(): void
    {
        $player  = self::getPlayer();
        $gobelin = self::getGoblin();

        $conditions = self::getConditionFactory()->from(
            [
                'numberRequired' => 1,
                'conditions'     => [
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
            new Modifier('self.ability.vitality.value', 2),
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
