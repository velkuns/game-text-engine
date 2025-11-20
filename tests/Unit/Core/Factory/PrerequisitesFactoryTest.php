<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Core\Factory;

use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;

class PrerequisitesFactoryTest extends TestCase
{
    use FactoryTrait;

    public function testFrom(): void
    {
        $data = [
            'numberRequired' => 1,
            'requirements'   => [
                [
                    'type'      => 'self.attribute.strength',
                    'condition' => 'value>=10',
                    'is'        => true,
                ],
                [
                    'type'      => 'self.trait.skill',
                    'condition' => 'name=Sword (Mastery)',
                    'is'        => false,
                ],
                [
                    'type'      => 'self.inventory.item',
                    'condition' => 'subType=sword;equipped=true;flags&3',
                    'is'        => true,
                ],
            ],
        ];

        $conditions = self::getPrerequisitesFactory()->from($data);

        self::assertNotNull($conditions);
        self::assertSame(1, $conditions->getNumberRequired());

        $condition0 = $conditions->getRequirements()[0];
        self::assertSame('self.attribute.strength', $condition0->getType());
        self::assertSame('value>=10', $condition0->getCondition());
        self::assertTrue($condition0->is());

        $condition1 = $conditions->getRequirements()[1];
        self::assertSame('self.trait.skill', $condition1->getType());
        self::assertSame('name=Sword (Mastery)', $condition1->getCondition());
        self::assertFalse($condition1->is());

        $condition2 = $conditions->getRequirements()[2];
        self::assertSame('self.inventory.item', $condition2->getType());
        self::assertSame('subType=sword;equipped=true;flags&3', $condition2->getCondition());
        self::assertTrue($condition2->is());

        self::assertSame($data, $conditions->jsonSerialize());
    }

    public function testFromWithNullData(): void
    {
        $data = null;

        $conditions = self::getPrerequisitesFactory()->from($data);

        self::assertNull($conditions);
    }
}
