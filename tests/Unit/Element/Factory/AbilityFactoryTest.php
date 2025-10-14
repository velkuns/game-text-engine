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
            'strength' => self::getAbilityBaseData('strength'),
            'agility'  => self::getAbilityBaseData('agility'),
        ];

        $bases = $this->factory->fromBases($data);

        self::assertCount(2, $bases);

        $strength = $bases['strength'];
        self::assertSame('strength', $strength->getName());
        self::assertSame(AbilityType::Base, $strength->getType());
        self::assertSame(10, $strength->getCurrent());
        self::assertSame(20, $strength->getMax());
        self::assertSame(10, $strength->getInitial());
        self::assertSame(0, $strength->getConstraints()->min);
        self::assertSame(100, $strength->getConstraints()->max);
        self::assertSame($data['strength'], $strength->jsonSerialize());
        self::assertNull($strength->getRule());

        $agility = $bases['agility'];
        self::assertSame('agility', $agility->getName());
        self::assertSame(AbilityType::Base, $agility->getType());
        self::assertSame(15, $agility->getCurrent());
        self::assertSame(30, $agility->getMax());
        self::assertSame(15, $agility->getInitial());
        self::assertSame(0, $agility->getConstraints()->min);
        self::assertSame(100, $agility->getConstraints()->max);
        self::assertSame($data['agility'], $agility->jsonSerialize());
        self::assertNull($agility->getRule());
    }

    public function testFromBaseAbility(): void
    {
        $data = self::getAbilityBaseData('strength');

        $strength = $this->factory->fromBaseAbility($data);

        self::assertSame('strength', $strength->getName());
        self::assertSame(AbilityType::Base, $strength->getType());
        self::assertSame(10, $strength->getCurrent());
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
        self::assertSame(25, $attack->getCurrent());
        self::assertSame(50, $attack->getMax());
        self::assertSame(25, $attack->getInitial());
        self::assertSame(0, $attack->getConstraints()->min);
        self::assertSame(200, $attack->getConstraints()->max);
        self::assertSame($data['attack'], $attack->jsonSerialize());

        $defense = $compounds['defense'];
        self::assertSame('defense', $defense->getName());
        self::assertSame(AbilityType::Compound, $defense->getType());
        self::assertSame('endurance + intuition', $defense->getRule());
        self::assertSame(20, $defense->getCurrent());
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
        self::assertSame(25, $attack->getCurrent());
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
            'strength' => [
                'type'    => 'base',
                'name'    => 'strength',
                'current' => 10,
                'max'     => 20,
                'constraints' => [
                    'min' => 0,
                    'max' => 100,
                ],
                'initial' => 10,
                'rule'    => null,
            ],
            'agility' => [
                'type'    => 'base',
                'name'    => 'agility',
                'current' => 15,
                'max'     => 30,
                'constraints' => [
                    'min' => 0,
                    'max' => 100,
                ],
                'initial' => 15,
                'rule'    => null,
            ],
            'endurance' => [
                'type'    => 'base',
                'name'    => 'endurance',
                'current' => 12,
                'max'     => 25,
                'constraints' => [
                    'min' => 0,
                    'max' => 100,
                ],
                'initial' => 12,
                'rule'    => null,
            ],
            'intuition' => [
                'type'    => 'base',
                'name'    => 'intuition',
                'current' => 8,
                'max'     => 20,
                'constraints' => [
                    'min' => 0,
                    'max' => 100,
                ],
                'initial' => 8,
                'rule'    => null,
            ],
        ];

        return $bases[$name];
    }
}
