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
use Velkuns\GameTextEngine\Api\Items;
use Velkuns\GameTextEngine\Api\Player;
use Velkuns\GameTextEngine\Element\Item\ItemInterface;
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

        $playerApi = new Player(self::getEntityFactory(), $items);

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
}
