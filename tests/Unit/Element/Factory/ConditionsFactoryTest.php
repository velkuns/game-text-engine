<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Element\Factory;

use Velkuns\GameTextEngine\Element\Condition\ConditionOperatorType;
use Velkuns\GameTextEngine\Element\Factory\ConditionsFactory;
use PHPUnit\Framework\TestCase;

class ConditionsFactoryTest extends TestCase
{
    private ConditionsFactory $factory;

    public function setUp(): void
    {
        $this->factory = new ConditionsFactory();
    }

    public function testFrom(): void
    {
        $data = [
            'numberRequired' => 1,
            'conditions' => [
                [
                    'type'     => 'self.ability',
                    'name'     => 'strength',
                    'operator' => '>=',
                    'value'    => 10,
                    'subType'  => null,
                    'equipped' => null,
                    'flags'    => null,
                ],
                [
                    'type'     => 'self.statuses.skill',
                    'name'     => 'sword mastery',
                    'operator' => '=',
                    'value'    => 0,
                    'subType'  => null,
                    'equipped' => null,
                    'flags'    => null,
                ],
                [
                    'type'     => 'self.inventory.item',
                    'name'     => '',
                    'operator' => '=',
                    'value'    => 1,
                    'subType'  => 'sword',
                    'equipped' => true,
                    'flags'    => 3,
                ],
            ],
        ];

        $conditions = $this->factory->from($data);

        self::assertNotNull($conditions);
        self::assertSame(1, $conditions->getNumberRequired());

        $condition0 = $conditions->getConditions()[0];
        self::assertSame('strength', $condition0->getName());
        self::assertSame('self.ability', $condition0->getType());
        self::assertSame(10, $condition0->getValue());
        self::assertSame(ConditionOperatorType::GreaterOrEqualThan, $condition0->getOperator());
        self::assertNull($condition0->isEquipped());
        self::assertNull($condition0->getFlags());
        self::assertNull($condition0->getSubType());

        $condition1 = $conditions->getConditions()[1];
        self::assertSame('sword mastery', $condition1->getName());
        self::assertSame('self.statuses.skill', $condition1->getType());
        self::assertSame(0, $condition1->getValue());
        self::assertSame(ConditionOperatorType::Equal, $condition1->getOperator());
        self::assertNull($condition1->isEquipped());
        self::assertNull($condition1->getFlags());
        self::assertNull($condition1->getSubType());

        $condition2 = $conditions->getConditions()[2];
        self::assertSame('', $condition2->getName());
        self::assertSame('self.inventory.item', $condition2->getType());
        self::assertSame(1, $condition2->getValue());
        self::assertSame(ConditionOperatorType::Equal, $condition2->getOperator());
        self::assertTrue($condition2->isEquipped());
        self::assertSame(3, $condition2->getFlags());
        self::assertSame('sword', $condition2->getSubType());

        self::assertSame($data, $conditions->jsonSerialize());
    }

    public function testFromWithNullData(): void
    {
        $data = null;

        $conditions = $this->factory->from($data);

        self::assertNull($conditions);
    }
}
