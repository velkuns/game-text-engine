<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Element\Ability;

use Velkuns\GameTextEngine\Element\Ability\AbilityType;
use Velkuns\GameTextEngine\Element\Ability\BaseAbility;
use Velkuns\GameTextEngine\Element\Ability\CompoundAbility;
use Velkuns\GameTextEngine\Element\Ability\ConstraintsAbility;
use Velkuns\GameTextEngine\Element\Modifier\Modifier;
use PHPUnit\Framework\TestCase;

class CompoundAbilityTest extends TestCase
{
    public function testGetter(): void
    {
        $ability = new CompoundAbility(
            name: 'attack',
            rule: 'strength + agility',
            abilities: $this->getBaseAbilities(),
        );

        self::assertSame(AbilityType::Compound, $ability->getType());
        self::assertSame('attack', $ability->getName());
        self::assertSame(5, $ability->getValue());
        self::assertSame(11, $ability->getMax());
        self::assertSame(10, $ability->getInitial());
        self::assertSame('strength + agility', $ability->getRule());
        self::assertSame(0, $ability->getConstraints()->min);
        self::assertSame(20, $ability->getConstraints()->max);
    }

    public function testValueValueWithModifiers(): void
    {
        $ability = new CompoundAbility(
            name: 'attack',
            rule: 'strength + agility',
            abilities: $this->getBaseAbilities(),
        );

        self::assertSame(5, $ability->getValue());
        self::assertSame(11, $ability->getMax());

        $modifiers = [
            new Modifier('attack', 2),
            new Modifier('attack', -1),
            new Modifier('defense', 5), // Should be ignored
        ];

        self::assertSame(6, $ability->getValueWithModifiers($modifiers)); // 5 + 2 - 1 = 6
    }

    /**
     * @return array<string, BaseAbility>
     */
    private function getBaseAbilities(): array
    {
        return [
            'strength' => new BaseAbility(
                name: 'strength',
                value: 3,
                max: 6,
                constraints: new ConstraintsAbility(min: 0, max: 10),
                initial: 5,
            ),
            'agility'  => new BaseAbility(
                name: 'agility',
                value: 2,
                max: 5,
                constraints: new ConstraintsAbility(min: 0, max: 10),
                initial: 5,
            ),
        ];
    }
}
