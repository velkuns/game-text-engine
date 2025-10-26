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
use Velkuns\GameTextEngine\Api\Items;
use Velkuns\GameTextEngine\Api\Player;
use Velkuns\GameTextEngine\Element\Item\ItemInterface;
use Velkuns\GameTextEngine\Element\Modifier\ModifierProcessor;
use Velkuns\GameTextEngine\Element\Resolver\TypeElementResolver;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;
use Velkuns\GameTextEngine\Utils\Loader\JsonLoader;

/**
 * @phpstan-import-type ItemData from ItemInterface
 */
class PlayerTest extends TestCase
{
    use EntityTrait;
    use FactoryTrait;

    public function testNew(): void
    {
        $dataDir = __DIR__ . '/../../../data';
        $items   = new Items(self::getItemFactory());

        /** @var list<ItemData> $itemsData */
        $itemsData = (new JsonLoader())->fromFile($dataDir . '/items.json');
        $items->load($itemsData);

        $playerApi = new Player(self::getEntityFactory(), $items, new ModifierProcessor(new TypeElementResolver()));

        $newPlayerData = [
            'name'        => 'New Hero',
            'age'         => 25,
            'race'        => 'elf',
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
        $dataDir = __DIR__ . '/../../../data';
        $items   = new Items(self::getItemFactory());

        /** @var list<ItemData> $itemsData */
        $itemsData = (new JsonLoader())->fromFile($dataDir . '/items.json');
        $items->load($itemsData);

        $playerApi = new Player(self::getEntityFactory(), $items, new ModifierProcessor(new TypeElementResolver()));

        $newPlayerData = [
            'name'        => 'New Hero',
            'age'         => 25,
            'race'        => 'elf',
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
        $playerApi->player->getInventory()->get('Small Health Potion')?->setQuantity(2);
        $playerApi->consume('Small Health Potion'); // (11 + 12) +5 vitality => should be 28

        self::assertSame(28, $playerApi->player->getAbilities()->get('vitality')?->getValue());
        self::assertSame(1, $playerApi->player->getInventory()->get('Small Health Potion')?->getQuantity());

        $playerApi->consume('Small Health Potion');

        self::assertSame(33, $playerApi->player->getAbilities()->get('vitality')->getValue());
        self::assertNull($playerApi->player->getInventory()->get('Small Health Potion'));
    }

    public function testConsumeButItemNotInInventory(): void
    {
        $dataDir = __DIR__ . '/../../../data';
        $items   = new Items(self::getItemFactory());

        /** @var list<ItemData> $itemsData */
        $itemsData = (new JsonLoader())->fromFile($dataDir . '/items.json');
        $items->load($itemsData);

        $playerApi = new Player(self::getEntityFactory(), $items, new ModifierProcessor(new TypeElementResolver()));

        $newPlayerData = [
            'name'        => 'New Hero',
            'age'         => 25,
            'race'        => 'elf',
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
        $dataDir = __DIR__ . '/../../../data';
        $items   = new Items(self::getItemFactory());

        /** @var list<ItemData> $itemsData */
        $itemsData = (new JsonLoader())->fromFile($dataDir . '/items.json');
        $items->load($itemsData);

        $playerApi = new Player(self::getEntityFactory(), $items, new ModifierProcessor(new TypeElementResolver()));

        $newPlayerData = [
            'name'        => 'New Hero',
            'age'         => 25,
            'race'        => 'elf',
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
