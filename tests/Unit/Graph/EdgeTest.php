<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Graph;

use Velkuns\GameTextEngine\Graph\Edge;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;
use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;

class EdgeTest extends TestCase
{
    use EntityTrait;
    use FactoryTrait;

    public function testAvailable(): void
    {
        $player = self::getPlayer();
        $goblin = self::getGoblin();

        $data = [
            'numberRequired' => 1,
            'requirements'   => [
                [
                    'type'      => 'self.attribute.vitality',
                    'condition' => 'value<=0',
                    'is'        => true,
                ],
            ],
        ];
        $conditions = self::getPrerequisitesFactory()->from($data);

        $edge = new Edge('1', '2', 'You died!', $conditions);

        //~ Player is alive
        self::assertFalse($edge->available($player, $goblin));

        //~ Remove 100 vitality to player
        $player->getAttributes()->get('vitality')?->decrease(100);

        //~ Player is dead
        self::assertSame(0, $player->getAttributes()->get('vitality')?->getValue());
        self::assertTrue($edge->available($player, $goblin));
    }
}
