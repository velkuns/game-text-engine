<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Helper;

use Velkuns\GameTextEngine\Element\Entity\Entity;
use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Factory\AbilityFactory;
use Velkuns\GameTextEngine\Element\Factory\ConditionsFactory;
use Velkuns\GameTextEngine\Element\Factory\EntityFactory;
use Velkuns\GameTextEngine\Element\Factory\ItemFactory;
use Velkuns\GameTextEngine\Element\Factory\ModifierFactory;
use Velkuns\GameTextEngine\Element\Factory\StatusFactory;

/**
 * @phpstan-import-type EntityData from EntityInterface
 */
trait EntityTrait
{
    private EntityFactory $entityFactory;

    private function getEntityFactory(): EntityFactory
    {
        if (!isset($this->entityFactory)) {
            $this->entityFactory = new EntityFactory(
                new AbilityFactory(),
                new StatusFactory(new ModifierFactory(), new ConditionsFactory()),
                new ItemFactory(new ModifierFactory()),
            );
        }

        return $this->entityFactory;
    }

    private function getPlayer(): Entity
    {
        return $this->getEntityFactory()->from($this->getPlayerData());
    }

    private function getGoblin(): Entity
    {
        return $this->getEntityFactory()->from($this->getGoblinData());
    }

    /**
     * @phpstan-return EntityData
     */
    private function getPlayerData(): array
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
                        'initial' => 6,
                        'max'     => 6,
                        'current' => 6,
                        'constraints' => [
                            'min' => 0,
                            'max' => 12,
                        ],
                        'rule'    => null,
                    ],
                    'endurance' => [
                        'type'    => 'base',
                        'name'    => 'endurance',
                        'initial' => 8,
                        'max'     => 8,
                        'current' => 8,
                        'constraints' => [
                            'min' => 0,
                            'max' => 12,
                        ],
                        'rule'    => null,
                    ],
                    'agility' => [
                        'type'    => 'base',
                        'name'    => 'agility',
                        'initial' => 8,
                        'max'     => 8,
                        'current' => 8,
                        'constraints' => [
                            'min' => 0,
                            'max' => 12,
                        ],
                        'rule'    => null,
                    ],
                    'intuition' => [
                        'type'    => 'base',
                        'name'    => 'intuition',
                        'initial' => 7,
                        'max'     => 7,
                        'current' => 7,
                        'constraints' => [
                            'min' => 0,
                            'max' => 7,
                        ],
                        'rule'    => null,
                    ],
                    'vitality' => [
                        'type'    => 'base',
                        'name'    => 'vitality',
                        'initial' => 0,
                        'max'     => 0,
                        'current' => 0,
                        'constraints' => [
                            'min' => 0,
                            'max' => 24,
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
                    'swordsmanship' => [
                        'type'        => 'skill',
                        'name'        => 'swordsmanship',
                        'description' => 'Super skill',
                        'modifiers'   => [
                            [
                                'ability' => 'agility',
                                'value'   => 1,
                            ],
                            [
                                'ability' => 'attack',
                                'value'   => 2,
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
                'states'    => [
                    'Rested' => [
                        'type'        => 'state',
                        'name'        => 'Rested',
                        'description' => 'You are well rested',
                        'modifiers'   => [
                            [
                                'ability' => 'vitality',
                                'value'   => 0,
                            ],
                        ],
                        'conditions'  => null,
                        'durationTurns'  => 0,
                        'remainingTurns' => 0,
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
                    'flags'       => 7,
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
    private function getGoblinData(): array
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
                        'initial' => 4,
                        'max'     => 4,
                        'current' => 4,
                        'constraints' => [
                            'min' => 0,
                            'max' => 12,
                        ],
                        'rule'    => null,
                    ],
                    'endurance' => [
                        'type'    => 'base',
                        'name'    => 'endurance',
                        'initial' => 4,
                        'max'     => 4,
                        'current' => 4,
                        'constraints' => [
                            'min' => 0,
                            'max' => 12,
                        ],
                        'rule'    => null,
                    ],
                    'agility' => [
                        'type'    => 'base',
                        'name'    => 'agility',
                        'initial' => 7,
                        'max'     => 7,
                        'current' => 7,
                        'constraints' => [
                            'min' => 0,
                            'max' => 12,
                        ],
                        'rule'    => null,
                    ],
                    'intuition' => [
                        'type'    => 'base',
                        'name'    => 'intuition',
                        'initial' => 5,
                        'max'     => 5,
                        'current' => 5,
                        'constraints' => [
                            'min' => 0,
                            'max' => 12,
                        ],
                        'rule'    => null,
                    ],
                    'vitality' => [
                        'type'    => 'base',
                        'name'    => 'vitality',
                        'initial' => 0,
                        'max'     => 0,
                        'current' => 0,
                        'constraints' => [
                            'min' => 0,
                            'max' => 24,
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
