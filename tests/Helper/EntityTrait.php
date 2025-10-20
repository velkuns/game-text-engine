<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Helper;

use Velkuns\GameTextEngine\Element\Entity\EntityInterface;

/**
 * @phpstan-import-type EntityData from EntityInterface
 */
trait EntityTrait
{
    use FactoryTrait;

    private static function getPlayer(): EntityInterface
    {
        return self::getEntityFactory()->from(self::getPlayerData());
    }

    private static function getGoblin(): EntityInterface
    {
        return self::getEntityFactory()->from(self::getGoblinData());
    }

    /**
     * @phpstan-return EntityData
     */
    private static function getPlayerData(): array
    {
        return [
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
                        'initial' => 10,
                        'max'     => 10,
                        'value'   => 10,
                        'constraints' => [
                            'min' => 0,
                            'max' => 20,
                        ],
                        'rule'    => null,
                    ],
                    'endurance' => [
                        'type'    => 'base',
                        'name'    => 'endurance',
                        'initial' => 14,
                        'max'     => 14,
                        'value'   => 14,
                        'constraints' => [
                            'min' => 0,
                            'max' => 20,
                        ],
                        'rule'    => null,
                    ],
                    'agility' => [
                        'type'    => 'base',
                        'name'    => 'agility',
                        'initial' => 15,
                        'max'     => 15,
                        'value'   => 15,
                        'constraints' => [
                            'min' => 0,
                            'max' => 20,
                        ],
                        'rule'    => null,
                    ],
                    'intuition' => [
                        'type'    => 'base',
                        'name'    => 'intuition',
                        'initial' => 12,
                        'max'     => 12,
                        'value'   => 12,
                        'constraints' => [
                            'min' => 0,
                            'max' => 20,
                        ],
                        'rule'    => null,
                    ],
                    'vitality' => [
                        'type'    => 'base',
                        'name'    => 'vitality',
                        'initial' => 24,
                        'max'     => 24,
                        'value'   => 24,
                        'constraints' => [
                            'min' => 0,
                            'max' => 40,
                        ],
                        'rule'    => 'strength + endurance',
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
                    'Sword (Mastery)' => [
                        'type'        => 'skill',
                        'name'        => 'Sword (Mastery)',
                        'description' => 'Super skill',
                        'modifiers'   => [
                            [
                                'type'  => 'ability.agility',
                                'value' => 1,
                            ],
                            [
                                'type'  => 'ability.attack',
                                'value' => 2,
                            ],
                        ],
                        'conditions' => [
                            'numberRequired' => 1,
                            'conditions' => [
                                [
                                    'type'      => 'self.inventory.items',
                                    'condition' => 'subType=sword;equipped=true;flag&4',
                                    'is'        => true,
                                ],
                            ],
                        ],
                        'durationTurns'  => 0,
                        'remainingTurns' => 0,
                    ],
                ],
                'states'    => [
                    'Rested' => [
                        'type'        => 'state',
                        'name'        => 'Rested',
                        'description' => 'You are well rested',
                        'modifiers'   => [
                            [
                                'type'  => 'ability.vitality',
                                'value' => 2,
                            ],
                        ],
                        'conditions'  => null,
                        'durationTurns'  => 3,
                        'remainingTurns' => 1,
                    ],
                ],
                'blessings' => [],
                'curses'    => [],
                'titles'    => [
                    'Dragon Slayer' => [
                        'type'        => 'title',
                        'name'        => 'Dragon Slayer',
                        'description' => 'You have slain a dragon',
                        'modifiers'   => [],
                        'conditions'  => null,
                        'durationTurns'  => 0,
                        'remainingTurns' => 0,
                    ],
                ],
            ],
            'inventory' => [
                [
                    'type'        => 'item',
                    'name'        => 'The Axe',
                    'subType'     => 'axe',
                    'description' => 'A sharp axe',
                    'modifiers'   => [],
                    'flags'       => 7,
                    'equipped'    => false,
                    'damages'     => 2,
                    'price'       => 0,
                ],
                [
                    'type'        => 'item',
                    'name'        => 'The Sword',
                    'subType'     => 'sword',
                    'description' => 'A sharp blade',
                    'modifiers'   => [],
                    'flags'       => 6,
                    'equipped'    => true,
                    'damages'     => 2,
                    'price'       => 0,
                ],
            ],
        ];
    }

    /**
     * @phpstan-return EntityData
     */
    private static function getGoblinData(): array
    {
        return [
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
                        'initial' => 8,
                        'max'     => 8,
                        'value'   => 8,
                        'constraints' => [
                            'min' => 0,
                            'max' => 20,
                        ],
                        'rule'    => null,
                    ],
                    'endurance' => [
                        'type'    => 'base',
                        'name'    => 'endurance',
                        'initial' => 8,
                        'max'     => 8,
                        'value'   => 8,
                        'constraints' => [
                            'min' => 0,
                            'max' => 20,
                        ],
                        'rule'    => null,
                    ],
                    'agility' => [
                        'type'    => 'base',
                        'name'    => 'agility',
                        'initial' => 14,
                        'max'     => 14,
                        'value'   => 14,
                        'constraints' => [
                            'min' => 0,
                            'max' => 20,
                        ],
                        'rule'    => null,
                    ],
                    'intuition' => [
                        'type'    => 'base',
                        'name'    => 'intuition',
                        'initial' => 10,
                        'max'     => 10,
                        'value'   => 10,
                        'constraints' => [
                            'min' => 0,
                            'max' => 20,
                        ],
                        'rule'    => null,
                    ],
                    'vitality' => [
                        'type'    => 'base',
                        'name'    => 'vitality',
                        'initial' => 0,
                        'max'     => 0,
                        'value'   => 0,
                        'constraints' => [
                            'min' => 0,
                            'max' => 40,
                        ],
                        'rule'    => 'strength + endurance',
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
    }
}
