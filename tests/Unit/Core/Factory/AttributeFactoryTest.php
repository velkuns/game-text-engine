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
use Velkuns\GameTextEngine\Core\Factory\AttributeFactory;
use Velkuns\GameTextEngine\Rpg\Attribute\AttributeType;
use Velkuns\GameTextEngine\Rpg\Attribute\SimpleAttribute;

/**
 * @phpstan-import-type SimpleAttributeData from SimpleAttribute
 */
class AttributeFactoryTest extends TestCase
{
    private AttributeFactory $factory;

    public function setUp(): void
    {
        $this->factory = new AttributeFactory();
    }

    public function testFromSimples(): void
    {
        $data = [
            // With initialization
            'vitality'  => self::getAttributeSimpleData('vitality'),
            // Without initialization
            'strength'  => self::getAttributeSimpleData('strength'),
            'endurance' => self::getAttributeSimpleData('endurance'),
            'agility'   => self::getAttributeSimpleData('agility'),
        ];

        $simples = $this->factory->fromSimples($data);

        self::assertCount(4, $simples);

        $strength = $simples['strength'];
        self::assertSame('strength', $strength->getName());
        self::assertSame(AttributeType::Simple, $strength->getType());
        self::assertSame(10, $strength->getValue());
        self::assertSame(20, $strength->getMax());
        self::assertSame(10, $strength->getInitial());
        self::assertSame(0, $strength->getConstraints()->min);
        self::assertSame(100, $strength->getConstraints()->max);
        self::assertSame($data['strength'], $strength->jsonSerialize());
        self::assertNull($strength->getRule());

        $agility = $simples['agility'];
        self::assertSame('agility', $agility->getName());
        self::assertSame(AttributeType::Simple, $agility->getType());
        self::assertSame(15, $agility->getValue());
        self::assertSame(30, $agility->getMax());
        self::assertSame(15, $agility->getInitial());
        self::assertSame(0, $agility->getConstraints()->min);
        self::assertSame(100, $agility->getConstraints()->max);
        self::assertSame($data['agility'], $agility->jsonSerialize());
        self::assertNull($agility->getRule());

        $vitality = $simples['vitality'];
        self::assertSame('vitality', $vitality->getName());
        self::assertSame(AttributeType::Simple, $vitality->getType());
        self::assertSame(22, $vitality->getValue());
        self::assertSame(22, $vitality->getMax());
        self::assertSame(22, $vitality->getInitial());
        self::assertSame(0, $vitality->getConstraints()->min);
        self::assertSame(100, $vitality->getConstraints()->max);
        self::assertSame('endurance + strength', $vitality->getRule());

        $expectedVitalityData = [
            'type'        => 'simple',
            'name'        => 'vitality',
            'value'     => 22,
            'max'         => 22,
            'constraints' => [
                'min' => 0,
                'max' => 100,
            ],
            'initial'     => 22,
            'rule'        => 'endurance + strength',
        ];
        self::assertEquals($expectedVitalityData, $vitality->jsonSerialize());
    }

    public function testFromSimpleAttribute(): void
    {
        $data = self::getAttributeSimpleData('strength');

        $strength = $this->factory->fromSimpleAttribute($data);

        self::assertSame('strength', $strength->getName());
        self::assertSame(AttributeType::Simple, $strength->getType());
        self::assertSame(10, $strength->getValue());
        self::assertSame(20, $strength->getMax());
        self::assertSame(10, $strength->getInitial());
        self::assertSame(0, $strength->getConstraints()->min);
        self::assertSame(100, $strength->getConstraints()->max);
        self::assertSame($data, $strength->jsonSerialize());
        self::assertNull($strength->getRule());
    }

    public function testFromCompounds(): void
    {
        $simples = [
            'strength'  => $this->factory->fromSimpleAttribute(self::getAttributeSimpleData('strength')),
            'agility'   => $this->factory->fromSimpleAttribute(self::getAttributeSimpleData('agility')),
            'endurance' => $this->factory->fromSimpleAttribute(self::getAttributeSimpleData('endurance')),
            'intuition' => $this->factory->fromSimpleAttribute(self::getAttributeSimpleData('intuition')),
        ];

        $data = [
            'attack' => [
                'type' => 'compound',
                'name' => 'attack',
                'rule' => 'strength + agility',
            ],
            'defense' => [
                'type' => 'compound',
                'name' => 'defense',
                'rule' => 'endurance + intuition',
            ],
        ];

        $compounds = $this->factory->fromCompounds($data, $simples);
        self::assertCount(2, $compounds);

        $attack = $compounds['attack'];
        self::assertSame('attack', $attack->getName());
        self::assertSame(AttributeType::Compound, $attack->getType());
        self::assertSame('strength + agility', $attack->getRule());
        self::assertSame(25, $attack->getValue());
        self::assertSame(50, $attack->getMax());
        self::assertSame(25, $attack->getInitial());
        self::assertSame(0, $attack->getConstraints()->min);
        self::assertSame(200, $attack->getConstraints()->max);
        self::assertSame($data['attack'], $attack->jsonSerialize());

        $defense = $compounds['defense'];
        self::assertSame('defense', $defense->getName());
        self::assertSame(AttributeType::Compound, $defense->getType());
        self::assertSame('endurance + intuition', $defense->getRule());
        self::assertSame(20, $defense->getValue());
        self::assertSame(45, $defense->getMax());
        self::assertSame(20, $defense->getInitial());
        self::assertSame(0, $defense->getConstraints()->min);
        self::assertSame(200, $defense->getConstraints()->max);
        self::assertSame($data['defense'], $defense->jsonSerialize());
    }

    public function testFromCompoundAttribute(): void
    {
        $simples = [
            'strength'  => $this->factory->fromSimpleAttribute(self::getAttributeSimpleData('strength')),
            'agility'   => $this->factory->fromSimpleAttribute(self::getAttributeSimpleData('agility')),
        ];

        $data = [
            'type' => 'compound',
            'name' => 'attack',
            'rule' => 'strength + agility',
        ];

        $attack = $this->factory->fromCompoundAttribute($data, $simples);
        self::assertSame('attack', $attack->getName());
        self::assertSame(AttributeType::Compound, $attack->getType());
        self::assertSame('strength + agility', $attack->getRule());
        self::assertSame(25, $attack->getValue());
        self::assertSame(50, $attack->getMax());
        self::assertSame(25, $attack->getInitial());
        self::assertSame(0, $attack->getConstraints()->min);
        self::assertSame(200, $attack->getConstraints()->max);
        self::assertSame($data, $attack->jsonSerialize());
    }

    /**
     * @return SimpleAttributeData
     */
    private static function getAttributeSimpleData(string $name): array
    {
        $simples = [
            'vitality' => [
                'type'    => 'simple',
                'name'    => 'vitality',
                'initial' => 0,
                'max'     => 0,
                'value'   => 0,
                'constraints' => [
                    'min' => 0,
                    'max' => 100,
                ],
                'rule'    => 'endurance + strength',
            ],
            'strength' => [
                'type'    => 'simple',
                'name'    => 'strength',
                'initial' => 10,
                'max'     => 20,
                'value'   => 10,
                'constraints' => [
                    'min' => 0,
                    'max' => 100,
                ],
                'rule'    => null,
            ],
            'agility' => [
                'type'    => 'simple',
                'name'    => 'agility',
                'initial' => 15,
                'max'     => 30,
                'value'   => 15,
                'constraints' => [
                    'min' => 0,
                    'max' => 100,
                ],
                'rule'    => null,
            ],
            'endurance' => [
                'type'    => 'simple',
                'name'    => 'endurance',
                'initial' => 12,
                'max'     => 25,
                'value'   => 12,
                'constraints' => [
                    'min' => 0,
                    'max' => 100,
                ],
                'rule'    => null,
            ],
            'intuition' => [
                'type'    => 'simple',
                'name'    => 'intuition',
                'initial' => 8,
                'max'     => 20,
                'value'   => 8,
                'constraints' => [
                    'min' => 0,
                    'max' => 100,
                ],
                'rule'    => null,
            ],
        ];

        return $simples[$name];
    }
}
