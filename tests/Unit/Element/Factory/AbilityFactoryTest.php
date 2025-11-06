<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Element\Factory;

use Velkuns\GameTextEngine\Element\Ability\AbilityType;
use Velkuns\GameTextEngine\Element\Ability\BaseAbility;
use Velkuns\GameTextEngine\Element\Factory\AbilityFactory;
use PHPUnit\Framework\TestCase;

/**
 * @phpstan-import-type BaseAbilityData from BaseAbility
 */
class AbilityFactoryTest extends TestCase
{
    private AbilityFactory $factory;

    public function setUp(): void
    {
        $this->factory = new AbilityFactory();
    }

    public function testFromBases(): void
    {
        $data = [
            // With initialization
            'vitality'  => self::getAbilityBaseData('vitality'),
            // Without initialization
            'strength'  => self::getAbilityBaseData('strength'),
            'endurance' => self::getAbilityBaseData('endurance'),
            'agility'   => self::getAbilityBaseData('agility'),
        ];

        $bases = $this->factory->fromBases($data);

        self::assertCount(4, $bases);

        $strength = $bases['strength'];
        self::assertSame('strength', $strength->getName());
        self::assertSame(AbilityType::Base, $strength->getType());
        self::assertSame(10, $strength->getValue());
        self::assertSame(20, $strength->getMax());
        self::assertSame(10, $strength->getInitial());
        self::assertSame(0, $strength->getConstraints()->min);
        self::assertSame(100, $strength->getConstraints()->max);
        self::assertSame($data['strength'], $strength->jsonSerialize());
        self::assertNull($strength->getRule());

        $agility = $bases['agility'];
        self::assertSame('agility', $agility->getName());
        self::assertSame(AbilityType::Base, $agility->getType());
        self::assertSame(15, $agility->getValue());
        self::assertSame(30, $agility->getMax());
        self::assertSame(15, $agility->getInitial());
        self::assertSame(0, $agility->getConstraints()->min);
        self::assertSame(100, $agility->getConstraints()->max);
        self::assertSame($data['agility'], $agility->jsonSerialize());
        self::assertNull($agility->getRule());

        $vitality = $bases['vitality'];
        self::assertSame('vitality', $vitality->getName());
        self::assertSame(AbilityType::Base, $vitality->getType());
        self::assertSame(22, $vitality->getValue());
        self::assertSame(22, $vitality->getMax());
        self::assertSame(22, $vitality->getInitial());
        self::assertSame(0, $vitality->getConstraints()->min);
        self::assertSame(100, $vitality->getConstraints()->max);
        self::assertSame('endurance + strength', $vitality->getRule());

        $expectedVitalityData = [
            'type'        => 'base',
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

    public function testFromBaseAbility(): void
    {
        $data = self::getAbilityBaseData('strength');

        $strength = $this->factory->fromBaseAbility($data);

        self::assertSame('strength', $strength->getName());
        self::assertSame(AbilityType::Base, $strength->getType());
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
        $bases = [
            'strength'  => $this->factory->fromBaseAbility(self::getAbilityBaseData('strength')),
            'agility'   => $this->factory->fromBaseAbility(self::getAbilityBaseData('agility')),
            'endurance' => $this->factory->fromBaseAbility(self::getAbilityBaseData('endurance')),
            'intuition' => $this->factory->fromBaseAbility(self::getAbilityBaseData('intuition')),
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

        $compounds = $this->factory->fromCompounds($data, $bases);
        self::assertCount(2, $compounds);

        $attack = $compounds['attack'];
        self::assertSame('attack', $attack->getName());
        self::assertSame(AbilityType::Compound, $attack->getType());
        self::assertSame('strength + agility', $attack->getRule());
        self::assertSame(25, $attack->getValue());
        self::assertSame(50, $attack->getMax());
        self::assertSame(25, $attack->getInitial());
        self::assertSame(0, $attack->getConstraints()->min);
        self::assertSame(200, $attack->getConstraints()->max);
        self::assertSame($data['attack'], $attack->jsonSerialize());

        $defense = $compounds['defense'];
        self::assertSame('defense', $defense->getName());
        self::assertSame(AbilityType::Compound, $defense->getType());
        self::assertSame('endurance + intuition', $defense->getRule());
        self::assertSame(20, $defense->getValue());
        self::assertSame(45, $defense->getMax());
        self::assertSame(20, $defense->getInitial());
        self::assertSame(0, $defense->getConstraints()->min);
        self::assertSame(200, $defense->getConstraints()->max);
        self::assertSame($data['defense'], $defense->jsonSerialize());
    }

    public function testFromCompoundAbility(): void
    {
        $bases = [
            'strength'  => $this->factory->fromBaseAbility(self::getAbilityBaseData('strength')),
            'agility'   => $this->factory->fromBaseAbility(self::getAbilityBaseData('agility')),
        ];

        $data = [
            'type' => 'compound',
            'name' => 'attack',
            'rule' => 'strength + agility',
        ];

        $attack = $this->factory->fromCompoundAbility($data, $bases);
        self::assertSame('attack', $attack->getName());
        self::assertSame(AbilityType::Compound, $attack->getType());
        self::assertSame('strength + agility', $attack->getRule());
        self::assertSame(25, $attack->getValue());
        self::assertSame(50, $attack->getMax());
        self::assertSame(25, $attack->getInitial());
        self::assertSame(0, $attack->getConstraints()->min);
        self::assertSame(200, $attack->getConstraints()->max);
        self::assertSame($data, $attack->jsonSerialize());
    }

    /**
     * @return BaseAbilityData
     */
    private static function getAbilityBaseData(string $name): array
    {
        $bases = [
            'vitality' => [
                'type'    => 'base',
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
                'type'    => 'base',
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
                'type'    => 'base',
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
                'type'    => 'base',
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
                'type'    => 'base',
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

        return $bases[$name];
    }
}
