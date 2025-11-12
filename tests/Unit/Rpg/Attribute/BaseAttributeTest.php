<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Rpg\Attribute;

use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Exception\Rpg\AttributeException;
use Velkuns\GameTextEngine\Rpg\Attribute\AttributeType;
use Velkuns\GameTextEngine\Rpg\Attribute\BaseAttribute;
use Velkuns\GameTextEngine\Rpg\Attribute\ConstraintsAttribute;
use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

class BaseAttributeTest extends TestCase
{
    public function testGetter(): void
    {
        $attribute = new BaseAttribute(
            name: 'vitality',
            value: 5,
            max: 10,
            constraints: new ConstraintsAttribute(min: 0, max: 20),
            initial: 10,
        );

        self::assertSame(AttributeType::Base, $attribute->getType());
        self::assertSame('vitality', $attribute->getName());
        self::assertSame(5, $attribute->getValue());
        self::assertSame(10, $attribute->getMax());
        self::assertSame(10, $attribute->getInitial());
        self::assertNull($attribute->getRule());
        self::assertSame(0, $attribute->getConstraints()->min);
        self::assertSame(20, $attribute->getConstraints()->max);
    }

    public function testInvalidBaseAttributeWithInitialization(): void
    {
        self::expectException(AttributeException::class);
        new BaseAttribute(
            name: 'vitality',
            value: 0,
            max: 0,
            constraints: new ConstraintsAttribute(min: 0, max: 20),
            initial: 0,
            rule: null,
            attributes: [],
        );
    }

    public function testMutattributeIncrease(): void
    {
        $attribute = new BaseAttribute(
            name: 'vitality',
            value: 5,
            max: 10,
            constraints: new ConstraintsAttribute(min: 0, max: 20),
            initial: 10,
        );

        self::assertSame(5, $attribute->getValue());
        self::assertSame(10, $attribute->getMax());

        $attribute->increase(5);
        $attribute->increaseMax(5);

        self::assertSame(10, $attribute->getValue());
        self::assertSame(15, $attribute->getMax());
    }

    public function testMutattributeIncreaseOverConstraint(): void
    {
        $attribute = new BaseAttribute(
            name: 'vitality',
            value: 5,
            max: 10,
            constraints: new ConstraintsAttribute(min: 0, max: 20),
            initial: 10,
        );

        self::assertSame(5, $attribute->getValue());
        self::assertSame(10, $attribute->getMax());

        //~ Try increase over max constraint, so value should be clamped
        $attribute->increase(20);
        $attribute->increaseMax(20);

        self::assertSame(20, $attribute->getValue());
        self::assertSame(20, $attribute->getMax());
    }

    public function testMutattributeDecrease(): void
    {
        $attribute = new BaseAttribute(
            name: 'vitality',
            value: 5,
            max: 10,
            constraints: new ConstraintsAttribute(min: 0, max: 20),
            initial: 10,
        );

        self::assertSame(5, $attribute->getValue());
        self::assertSame(10, $attribute->getMax());

        $attribute->decrease(2);
        $attribute->decreaseMax(2);

        self::assertSame(3, $attribute->getValue());
        self::assertSame(8, $attribute->getMax());
    }

    public function testMutattributeDecreaseOverConstraint(): void
    {
        $attribute = new BaseAttribute(
            name: 'vitality',
            value: 5,
            max: 10,
            constraints: new ConstraintsAttribute(min: 0, max: 20),
            initial: 10,
        );

        self::assertSame(5, $attribute->getValue());
        self::assertSame(10, $attribute->getMax());

        //~ Try increase over max constraint, so value should be clamped
        $attribute->decrease(20);
        $attribute->decreaseMax(20);

        self::assertSame(0, $attribute->getValue());
        self::assertSame(0, $attribute->getMax());
    }

    public function testCurrentValueWithModifiers(): void
    {
        $attribute = new BaseAttribute(
            name: 'vitality',
            value: 5,
            max: 10,
            constraints: new ConstraintsAttribute(min: 0, max: 20),
            initial: 10,
        );

        self::assertSame(5, $attribute->getValue());
        self::assertSame(10, $attribute->getMax());

        $modifiers = [
            new Modifier('self.attribute.vitality.value', 2),
            new Modifier('self.attribute.vitality.value', -1),
            new Modifier('self.attribute.strength.value', 5), // Should be ignored
        ];

        self::assertSame(6, $attribute->getValueWithModifiers($modifiers)); // 5 + 2 - 1 = 6
    }
}
