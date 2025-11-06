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
use Velkuns\GameTextEngine\Api\Exception\PlayerException;
use Velkuns\GameTextEngine\Element\Item\ItemInterface;
use Velkuns\GameTextEngine\Tests\Helper\ApiTrait;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;

/**
 * @phpstan-import-type ItemData from ItemInterface
 */
class PlayerApiTest extends TestCase
{
    use ApiTrait;
    use EntityTrait;
    use FactoryTrait;

    public function testNew(): void
    {
        $playerApi = self::getPlayerApi();

        $newPlayerData = [
            'name'        => 'New Hero',
            'age'         => 25,
            'race'        => 'elf',
            'gender'      => 'non-binary',
            'description' => 'A brave hero',
            'background'  => 'Born in a small village',
            'abilities' => [
                'strength'  => 11,
                'endurance' => 12,
                'agility'   => 13,
                'intuition' => 14,
            ],
            'inventory' => ['Rusty Sword'],
        ];

        $expected = self::getPlayer(true);

        $playerApi->new($newPlayerData);

        self::assertEquals($expected, $playerApi->player);
    }

    public function testConsume(): void
    {
        $playerApi = self::getPlayerApi();

        $newPlayerData = [
            'name'        => 'New Hero',
            'age'         => 25,
            'race'        => 'elf',
            'gender'      => 'non-binary',
            'description' => 'A brave hero',
            'background'  => 'Born in a small village',
            'abilities' => [
                'strength'  => 11,
                'endurance' => 12,
                'agility'   => 13,
                'intuition' => 14,
            ],
            'inventory' => ['Rusty Sword', 'Small Health Potion'],
        ];

        $playerApi->new($newPlayerData);
        self::assertSame(23, $playerApi->player->getAbilities()->get('vitality')?->getValue());

        $playerApi->player->getInventory()->get('Small Health Potion')?->setQuantity(2);
        $playerApi->consume('Small Health Potion'); // (11 + 12) +5 vitality => should be 28

        self::assertSame(28, $playerApi->player->getAbilities()->get('vitality')->getValue());
        self::assertSame(1, $playerApi->player->getInventory()->get('Small Health Potion')?->getQuantity());

        $playerApi->consume('Small Health Potion');

        self::assertSame(33, $playerApi->player->getAbilities()->get('vitality')->getValue());
        self::assertNull($playerApi->player->getInventory()->get('Small Health Potion'));
    }

    public function testConsumeButItemNotInInventory(): void
    {
        $playerApi = self::getPlayerApi();

        $newPlayerData = [
            'name'        => 'New Hero',
            'age'         => 25,
            'race'        => 'elf',
            'gender'      => 'non-binary',
            'description' => 'A brave hero',
            'background'  => 'Born in a small village',
            'abilities' => [
                'strength'  => 11,
                'endurance' => 12,
                'agility'   => 13,
                'intuition' => 14,
            ],
            'inventory' => ['Rusty Sword', 'Small Health Potion'],
        ];

        $playerApi->new($newPlayerData);

        self::expectException(PlayerException::class);
        self::expectExceptionCode(1410);
        $playerApi->consume('Medium Health Potion');
    }

    public function testConsumeButItemIsNotConsumable(): void
    {
        $playerApi = self::getPlayerApi();

        $newPlayerData = [
            'name'        => 'New Hero',
            'age'         => 25,
            'race'        => 'elf',
            'gender'      => 'non-binary',
            'description' => 'A brave hero',
            'background'  => 'Born in a small village',
            'abilities' => [
                'strength'  => 11,
                'endurance' => 12,
                'agility'   => 13,
                'intuition' => 14,
            ],
            'inventory' => ['Rusty Sword', 'Small Health Potion'],
        ];

        $playerApi->new($newPlayerData);

        self::expectException(PlayerException::class);
        self::expectExceptionCode(1411);
        $playerApi->consume('Rusty Sword');
    }
}
