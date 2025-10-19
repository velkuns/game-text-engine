<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Element\Factory;

use Velkuns\GameTextEngine\Element\Condition\ConditionElementResolver;
use Velkuns\GameTextEngine\Element\Condition\ConditionParser;
use Velkuns\GameTextEngine\Element\Condition\ConditionValidator;
use Velkuns\GameTextEngine\Element\Factory\ConditionsFactory;
use PHPUnit\Framework\TestCase;

class ConditionsFactoryTest extends TestCase
{
    private ConditionsFactory $factory;

    public function setUp(): void
    {
        $this->factory = new ConditionsFactory(
            new ConditionParser(),
            new ConditionElementResolver(),
            new ConditionValidator(),
        );
    }

    public function testFrom(): void
    {
        $data = [
            'numberRequired' => 1,
            'conditions' => [
                [
                    'type'      => 'self.abilities.bases.strength',
                    'condition' => 'value>=10',
                    'is'        => true,
                ],
                [
                    'type'      => 'self.statuses.skills',
                    'condition' => 'name=Sword (Mastery)',
                    'is'        => false,
                ],
                [
                    'type'      => 'self.inventory.items',
                    'condition' => 'subType=sword;equipped=true;flags&3',
                    'is'        => true,
                ],
            ],
        ];

        $conditions = $this->factory->from($data);

        self::assertNotNull($conditions);
        self::assertSame(1, $conditions->getNumberRequired());

        $condition0 = $conditions->getConditions()[0];
        self::assertSame('self.abilities.bases.strength', $condition0->getType());
        self::assertSame('value>=10', $condition0->getCondition());
        self::assertTrue($condition0->is());

        $condition1 = $conditions->getConditions()[1];
        self::assertSame('self.statuses.skills', $condition1->getType());
        self::assertSame('name=Sword (Mastery)', $condition1->getCondition());
        self::assertFalse($condition1->is());

        $condition2 = $conditions->getConditions()[2];
        self::assertSame('self.inventory.items', $condition2->getType());
        self::assertSame('subType=sword;equipped=true;flags&3', $condition2->getCondition());
        self::assertTrue($condition2->is());

        self::assertSame($data, $conditions->jsonSerialize());
    }

    public function testFromWithNullData(): void
    {
        $data = null;

        $conditions = $this->factory->from($data);

        self::assertNull($conditions);
    }
}
