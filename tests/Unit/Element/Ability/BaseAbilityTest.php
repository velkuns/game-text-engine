<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Element\Ability;

use Velkuns\GameTextEngine\Element\Ability\AbilityType;
use Velkuns\GameTextEngine\Element\Ability\BaseAbility;
use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Element\Ability\ConstraintsAbility;
use Velkuns\GameTextEngine\Element\Modifier\Modifier;

class BaseAbilityTest extends TestCase
{
    public function testGetter(): void
    {
        $ability = new BaseAbility(
            name: 'vitality',
            value: 5,
            max: 10,
            constraints: new ConstraintsAbility(min: 0, max: 20),
            initial: 10,
        );

        self::assertSame(AbilityType::Base, $ability->getType());
        self::assertSame('vitality', $ability->getName());
        self::assertSame(5, $ability->getValue());
        self::assertSame(10, $ability->getMax());
        self::assertSame(10, $ability->getInitial());
        self::assertNull($ability->getRule());
        self::assertSame(0, $ability->getConstraints()->min);
        self::assertSame(20, $ability->getConstraints()->max);
    }

    public function testInvalidBaseAbilityWithInitialization(): void
    {
        self::expectException(\UnexpectedValueException::class);
        new BaseAbility(
            name: 'vitality',
            value: 0,
            max: 0,
            constraints: new ConstraintsAbility(min: 0, max: 20),
            initial: 0,
            rule: null,
            abilities: [],
        );
    }

    public function testMutabilityIncrease(): void
    {
        $ability = new BaseAbility(
            name: 'vitality',
            value: 5,
            max: 10,
            constraints: new ConstraintsAbility(min: 0, max: 20),
            initial: 10,
        );

        self::assertSame(5, $ability->getValue());
        self::assertSame(10, $ability->getMax());

        $ability->increase(5);
        $ability->increaseMax(5);

        self::assertSame(10, $ability->getValue());
        self::assertSame(15, $ability->getMax());
    }

    public function testMutabilityIncreaseOverConstraint(): void
    {
        $ability = new BaseAbility(
            name: 'vitality',
            value: 5,
            max: 10,
            constraints: new ConstraintsAbility(min: 0, max: 20),
            initial: 10,
        );

        self::assertSame(5, $ability->getValue());
        self::assertSame(10, $ability->getMax());

        //~ Try increase over max constraint, so value should be clamped
        $ability->increase(20);
        $ability->increaseMax(20);

        self::assertSame(20, $ability->getValue());
        self::assertSame(20, $ability->getMax());
    }

    public function testMutabilityDecrease(): void
    {
        $ability = new BaseAbility(
            name: 'vitality',
            value: 5,
            max: 10,
            constraints: new ConstraintsAbility(min: 0, max: 20),
            initial: 10,
        );

        self::assertSame(5, $ability->getValue());
        self::assertSame(10, $ability->getMax());

        $ability->decrease(2);
        $ability->decreaseMax(2);

        self::assertSame(3, $ability->getValue());
        self::assertSame(8, $ability->getMax());
    }

    public function testMutabilityDecreaseOverConstraint(): void
    {
        $ability = new BaseAbility(
            name: 'vitality',
            value: 5,
            max: 10,
            constraints: new ConstraintsAbility(min: 0, max: 20),
            initial: 10,
        );

        self::assertSame(5, $ability->getValue());
        self::assertSame(10, $ability->getMax());

        //~ Try increase over max constraint, so value should be clamped
        $ability->decrease(20);
        $ability->decreaseMax(20);

        self::assertSame(0, $ability->getValue());
        self::assertSame(0, $ability->getMax());
    }

    public function testCurrentValueWithModifiers(): void
    {
        $ability = new BaseAbility(
            name: 'vitality',
            value: 5,
            max: 10,
            constraints: new ConstraintsAbility(min: 0, max: 20),
            initial: 10,
        );

        self::assertSame(5, $ability->getValue());
        self::assertSame(10, $ability->getMax());

        $modifiers = [
            new Modifier('vitality', 2),
            new Modifier('vitality', -1),
            new Modifier('strength', 5), // Should be ignored
        ];

        self::assertSame(6, $ability->getValueWithModifiers($modifiers)); // 5 + 2 - 1 = 6
    }
}
