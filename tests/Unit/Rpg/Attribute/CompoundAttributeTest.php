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
use Velkuns\GameTextEngine\Rpg\Attribute\AttributeType;
use Velkuns\GameTextEngine\Rpg\Attribute\SimpleAttribute;
use Velkuns\GameTextEngine\Rpg\Attribute\CompoundAttribute;
use Velkuns\GameTextEngine\Rpg\Attribute\ConstraintsAttribute;
use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

class CompoundAttributeTest extends TestCase
{
    public function testGetter(): void
    {
        $attribute = new CompoundAttribute(
            name: 'attack',
            rule: 'strength + agility',
            attributes: $this->getSimpleAttributes(),
        );

        self::assertSame(AttributeType::Compound, $attribute->getType());
        self::assertSame('attack', $attribute->getName());
        self::assertSame(5, $attribute->getValue());
        self::assertSame(11, $attribute->getMax());
        self::assertSame(10, $attribute->getInitial());
        self::assertSame('strength + agility', $attribute->getRule());
        self::assertSame(0, $attribute->getConstraints()->min);
        self::assertSame(20, $attribute->getConstraints()->max);
    }

    public function testValueValueWithModifiers(): void
    {
        $attribute = new CompoundAttribute(
            name: 'attack',
            rule: 'strength + agility',
            attributes: $this->getSimpleAttributes(),
        );

        self::assertSame(5, $attribute->getValue());
        self::assertSame(11, $attribute->getMax());

        $modifiers = [
            new Modifier('self.attribute.attack.value', 2),
            new Modifier('self.attribute.attack.value', -1),
            new Modifier('self.attribute.defense.value', 5), // Should be ignored
        ];

        self::assertSame(6, $attribute->getValueWithModifiers($modifiers)); // 5 + 2 - 1 = 6
    }

    /**
     * @return array<string, SimpleAttribute>
     */
    private function getSimpleAttributes(): array
    {
        return [
            'strength' => new SimpleAttribute(
                name: 'strength',
                value: 3,
                max: 6,
                constraints: new ConstraintsAttribute(min: 0, max: 10),
                initial: 5,
            ),
            'agility'  => new SimpleAttribute(
                name: 'agility',
                value: 2,
                max: 5,
                constraints: new ConstraintsAttribute(min: 0, max: 10),
                initial: 5,
            ),
        ];
    }
}
