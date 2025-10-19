<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Element\Condition;

use PHPUnit\Framework\Attributes\DataProvider;
use Velkuns\GameTextEngine\Element\Condition\Conditions;
use Velkuns\GameTextEngine\Element\Entity\Entity;
use Velkuns\GameTextEngine\Element\Factory\AbilityFactory;
use Velkuns\GameTextEngine\Element\Factory\ConditionsFactory;
use Velkuns\GameTextEngine\Element\Factory\EntityFactory;
use Velkuns\GameTextEngine\Element\Factory\ItemFactory;
use Velkuns\GameTextEngine\Element\Factory\ModifierFactory;
use Velkuns\GameTextEngine\Element\Factory\StatusFactory;
use PHPUnit\Framework\TestCase;

class ConditionsTest extends TestCase
{
    private static ConditionsFactory $conditionFactory;
    private static EntityFactory $entityFactory;

    public static function setUpBeforeClass(): void
    {
        self::$conditionFactory = new ConditionsFactory();
        self::$entityFactory    = new EntityFactory(
            new AbilityFactory(),
            new StatusFactory(new ModifierFactory(), new ConditionsFactory()),
            new ItemFactory(new ModifierFactory()),
        );
    }

    #[DataProvider('evaluateDataProvider')]
    public function testEvaluate(Conditions $conditions, Entity $player, Entity $enemy, bool $evaluation): void
    {
        self::assertSame($evaluation, $conditions->evaluate($player, $enemy));
    }

    /**
     * @return array<string, array{0: Conditions|null, 1: Entity, 2: Entity, 3: bool}>
     */
    public static function evaluateDataProvider(): array
    {
        self::setUpBeforeClass();

        return [
            'evaluate required 1 condition with list of 1 conditions' => [
                self::$conditionFactory->from([
                    'numberRequired' => 1,
                    'conditions' => [
                        [
                            'type'     => 'ability',
                            'name'     => 'strength',
                            'operator' => '>=',
                            'value'    => 10,
                        ],
                    ],
                ]),
                self::getHero(),
                self::getEnemy(),
                true,
            ],
            'evaluate required 1 condition with list of 1 conditions but evaluation failed' => [
                self::$conditionFactory->from([
                    'numberRequired' => 1,
                    'conditions' => [
                        [
                            'type'     => 'skill',
                            'name'     => 'swordsmanship',
                            'operator' => '=',
                            'value'    => 0,
                        ],
                    ],
                ]),
                self::getHero(),
                self::getEnemy(),
                false,
            ],
            'evaluate required 1 condition with list of 2 conditions' => [
                self::$conditionFactory->from([
                    'numberRequired' => 1,
                    'conditions' => [
                        [
                            'type'     => 'ability',
                            'name'     => 'strength',
                            'operator' => '>',
                            'value'    => 10,
                        ],
                        [
                            'type'     => 'ability',
                            'name'     => 'agility',
                            'operator' => '=',
                            'value'    => 15,
                        ],
                    ],
                ]),
                self::getHero(),
                self::getEnemy(),
                true,
            ],
            'evaluate required 2 condition with list of 2 conditions' => [
                self::$conditionFactory->from([
                    'numberRequired' => 2,
                    'conditions' => [
                        [
                            'type'     => 'ability',
                            'name'     => 'strength',
                            'operator' => '<=',
                            'value'    => 10,
                        ],
                        [
                            'type'     => 'ability',
                            'name'     => 'agility',
                            'operator' => '>',
                            'value'    => 0,
                        ],
                    ],
                ]),
                self::getHero(),
                self::getEnemy(),
                true,
            ],
            'evaluate required 1 condition with list of 1 conditions on specific item' => [
                self::$conditionFactory->from([
                    'numberRequired' => 1,
                    'conditions' => [
                        [
                            'type'     => 'item',
                            'name'     => 'The Sword',
                            'subType'  => 'sword',
                            'operator' => '=',
                            'value'    => 1,
                            'equipped' => true,
                            'flags'    => 3,
                        ],
                    ],
                ]),
                self::getHero(),
                self::getEnemy(),
                true,
            ],
            'evaluate required 1 condition with list of 1 conditions on specific item but evaluation failed' => [
                self::$conditionFactory->from([
                    'numberRequired' => 1,
                    'conditions' => [
                        [
                            'type'     => 'item',
                            'name'     => 'The Sword',
                            'subType'  => 'sword',
                            'operator' => '=',
                            'value'    => 1,
                            'equipped' => false,
                            'flags'    => 3,
                        ],
                    ],
                ]),
                self::getHero(),
                self::getEnemy(),
                false,
            ],
        ];
    }

    public static function getHero(): Entity
    {
        $data = [
            'name'  => 'Brave Test Hero #1',
            'type'  => 'player',
            'coins' => 100,
            'info'  => [
                'level'       => 5,
                'age'         => 30,
                'size'        => '1m75',
                'race'        => 'elf',
                'description' => 'A brave hero',
                'background'  => 'Born in a small village',
                'notes'       => 'No special notes',
            ],
            'abilities' => [
                'bases' => [
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
                ],
                'compounds' => [
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
                ],
            ],
            'statuses' => [
                'skills' => [
                    'swordsmanship' => [
                        'type'        => 'skill',
                        'name'        => 'swordsmanship',
                        'description' => 'Super skill',
                        'modifiers'   => [
                            [
                                'ability' => 'agility',
                                'value'   => 5,
                            ],
                            [
                                'ability' => 'attack',
                                'value'   => 10,
                            ],
                        ],
                        'conditions' => [
                            'numberRequired' => 1,
                            'conditions'     => [
                                [
                                    'type'     => 'item',
                                    'name'     => '',
                                    'operator' => '=',
                                    'value'    => 1,
                                    'subType'  => 'sword',
                                    'equipped' => true,
                                    'flags'    => 3,
                                ],
                            ],
                        ],
                        'durationTurns'  => 0,
                        'remainingTurns' => 0,
                    ],
                ],
                'states'    => [],
                'blessings' => [],
                'curses'    => [],
                'titles'    => [],
            ],
            'inventory' => [
                [
                    'type'        => 'item',
                    'name'        => 'The Sword',
                    'subType'     => 'sword',
                    'description' => 'A sharp blade',
                    'modifiers'   => [],
                    'flags'       => 7,
                    'equipped'    => true,
                    'damages'     => 2,
                    'price'       => 0,
                ],
            ],
        ];

        return self::$entityFactory->from($data);
    }

    public static function getEnemy(): Entity
    {
        $data = [
            'name'  => 'Gobelin #1',
            'type'  => 'creature',
            'coins' => 2,
            'info'  => [
                'level'       => 2,
                'age'         => 20,
                'size'        => 'small',
                'race'        => 'gobelin',
                'description' => 'An evil gobelin',
                'background'  => 'Born in a small village',
                'notes'       => 'No special notes',
            ],
            'abilities' => [
                'bases' => [
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
                ],
                'compounds' => [
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
                ],
            ],
            'statuses' => [
                'skills'    => [],
                'states'    => [],
                'blessings' => [],
                'curses'    => [],
                'titles'    => [],
            ],
            'inventory' => [
                [
                    'type'        => 'item',
                    'name'        => 'The Dagger',
                    'subType'     => 'dagger',
                    'description' => 'A sharp dagger',
                    'modifiers'   => [],
                    'flags'       => 7,
                    'equipped'    => true,
                    'damages'     => 1,
                    'price'       => 0,
                ],
            ],
        ];

        return self::$entityFactory->from($data);
    }
}
