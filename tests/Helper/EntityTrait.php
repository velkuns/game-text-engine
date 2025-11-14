<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Helper;

use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;

/**
 * @phpstan-import-type EntityData from EntityInterface
 */
trait EntityTrait
{
    use FactoryTrait;

    private static function getPlayer(bool $new = false): EntityInterface
    {
        $data = $new ? self::getNewPlayerData() : self::getPlayerData();

        return self::getEntityFactory()
            ->from($data)
        ;
    }

    private static function getGoblin(): EntityInterface
    {
        return self::getEntityFactory()
            ->from(self::getGoblinData())
        ;
    }

    /**
     * @phpstan-return EntityData
     */
    private static function getPlayerData(): array
    {
        return [
            'name'      => 'Brave Test Hero #1',
            'type'      => 'player',
            'info'      => [
                'level'       => 5,
                'xp'          => 500,
                'age'         => 30,
                'size'        => '1m75',
                'race'        => 'elf',
                'gender'      => 'non-binary',
                'description' => 'A brave hero',
                'background'  => 'Born in a small village',
                'notes'       => 'No special notes',
            ],
            'damages'   => [
                'physical' => [
                    'type'  => 'physical',
                    'value' => 0,
                ],
            ],
            'attributes' => [
                'simples'     => [
                    'strength'  => [
                        'type'        => 'simple',
                        'name'        => 'strength',
                        'initial'     => 10,
                        'max'         => 10,
                        'value'       => 10,
                        'constraints' => [
                            'min' => 0,
                            'max' => 20,
                        ],
                        'rule'        => null,
                    ],
                    'endurance' => [
                        'type'        => 'simple',
                        'name'        => 'endurance',
                        'initial'     => 14,
                        'max'         => 14,
                        'value'       => 14,
                        'constraints' => [
                            'min' => 0,
                            'max' => 20,
                        ],
                        'rule'        => null,
                    ],
                    'agility'   => [
                        'type'        => 'simple',
                        'name'        => 'agility',
                        'initial'     => 15,
                        'max'         => 15,
                        'value'       => 15,
                        'constraints' => [
                            'min' => 0,
                            'max' => 20,
                        ],
                        'rule'        => null,
                    ],
                    'intuition' => [
                        'type'        => 'simple',
                        'name'        => 'intuition',
                        'initial'     => 12,
                        'max'         => 12,
                        'value'       => 12,
                        'constraints' => [
                            'min' => 0,
                            'max' => 20,
                        ],
                        'rule'        => null,
                    ],
                    'vitality'  => [
                        'type'        => 'simple',
                        'name'        => 'vitality',
                        'initial'     => 24,
                        'max'         => 24,
                        'value'       => 24,
                        'constraints' => [
                            'min' => 0,
                            'max' => 40,
                        ],
                        'rule'        => 'strength + endurance',
                    ],
                ],
                'compounds' => [
                    'attack'  => [
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
            'traits'  => [
                'skill'    => [
                    'Sword (Mastery)' => [
                        'type'           => 'skill',
                        'name'           => 'Sword (Mastery)',
                        'description'    => 'Super skill',
                        'modifiers'      => [
                            [
                                'type'  => 'self.attribute.agility.value',
                                'value' => 1,
                                'prerequisites'     => [
                                    'numberRequired' => 1,
                                    'requirements'     => [
                                        [
                                            'type'      => 'self.inventory.item',
                                            'condition' => 'subType=sword;equipped=true;flags&4',
                                            'is'        => true,
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'type'  => 'self.attribute.attack.value',
                                'value' => 2,
                                'prerequisites'     => [
                                    'numberRequired' => 1,
                                    'requirements'     => [
                                        [
                                            'type'      => 'self.inventory.item',
                                            'condition' => 'subType=sword;equipped=true;flags&4',
                                            'is'        => true,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'Dragon Hunter'   => [
                        'type'           => 'skill',
                        'name'           => 'Dragon Hunter',
                        'description'    => 'To help to chase dragon.',
                        'modifiers'      => [
                            [
                                'type'  => 'self.attribute.agility.value',
                                'value' => 1,
                                'prerequisites'     => [
                                    'numberRequired' => 1,
                                    'requirements'     => [
                                        [
                                            'type'      => 'enemy.info',
                                            'condition' => 'race=dragon',
                                            'is'        => true,
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'type'  => 'self.attribute.attack.value',
                                'value' => 2,
                                'prerequisites'     => [
                                    'numberRequired' => 1,
                                    'requirements'     => [
                                        [
                                            'type'      => 'enemy.info',
                                            'condition' => 'race=dragon',
                                            'is'        => true,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'Goblin Hunter'   => [
                        'type'           => 'skill',
                        'name'           => 'Goblin Hunter',
                        'description'    => 'To help to chase goblin.',
                        'modifiers'      => [
                            [
                                'type'  => 'self.damages.physical.value',
                                'value' => 1,
                                'prerequisites'     => [
                                    'numberRequired' => 1,
                                    'requirements'     => [
                                        [
                                            'type'      => 'enemy.info',
                                            'condition' => 'race=goblin',
                                            'is'        => true,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'state'    => [
                    'Rested' => [
                        'type'           => 'state',
                        'name'           => 'Rested',
                        'description'    => 'You are well rested',
                        'modifiers'      => [
                            [
                                'type'  => 'self.attribute.vitality.value',
                                'value' => 2,
                            ],
                        ],
                        'durationTurns'  => 3,
                        'remainingTurns' => 1,
                    ],
                ],
                'blessing' => [],
                'curse'    => [],
                'title'    => [
                    'Dragon Slayer' => [
                        'type'           => 'title',
                        'name'           => 'Dragon Slayer',
                        'description'    => 'You have slain a dragon',
                        'modifiers'      => [],
                    ],
                ],
            ],
            'inventory' => [
                'coins' => 100,
                'items' => [
                    [
                        'type'        => 'item',
                        'name'        => 'The Axe',
                        'subType'     => 'axe',
                        'description' => 'A sharp axe',
                        'modifiers'   => [],
                        'flags'       => 7,
                        'equipped'    => false,
                        'damages'     => ['physical' => ['type' => 'physical', 'value' => 2]],
                        'quantity'    => 1,
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
                        'damages'     => ['physical' => ['type' => 'physical', 'value' => 2]],
                        'quantity'    => 1,
                        'price'       => 0,
                    ],
                ],
            ],
        ];
    }

    /**
     * @phpstan-return EntityData
     */
    private static function getNewPlayerData(): array
    {
        return [
            'name'      => 'New Hero',
            'type'      => 'player',
            'info'      => [
                'level'       => 1,
                'xp'          => 0,
                'age'         => 25,
                'size'        => 'medium',
                'race'        => 'elf',
                'gender'      => 'non-binary',
                'description' => 'A brave hero',
                'background'  => 'Born in a small village',
                'notes'       => '',
            ],
            'damages'   => ['physical' => ['type' => 'physical', 'value' => 0]],
            'attributes' => [
                'simples'     => [
                    'strength'  => [
                        'type'        => 'simple',
                        'name'        => 'strength',
                        'initial'     => 11,
                        'max'         => 11,
                        'value'       => 11,
                        'constraints' => [
                            'min' => 0,
                            'max' => 20,
                        ],
                        'rule'        => null,
                    ],
                    'endurance' => [
                        'type'        => 'simple',
                        'name'        => 'endurance',
                        'initial'     => 12,
                        'max'         => 12,
                        'value'       => 12,
                        'constraints' => [
                            'min' => 0,
                            'max' => 20,
                        ],
                        'rule'        => null,
                    ],
                    'agility'   => [
                        'type'        => 'simple',
                        'name'        => 'agility',
                        'initial'     => 13,
                        'max'         => 13,
                        'value'       => 13,
                        'constraints' => [
                            'min' => 0,
                            'max' => 20,
                        ],
                        'rule'        => null,
                    ],
                    'intuition' => [
                        'type'        => 'simple',
                        'name'        => 'intuition',
                        'initial'     => 14,
                        'max'         => 14,
                        'value'       => 14,
                        'constraints' => [
                            'min' => 0,
                            'max' => 20,
                        ],
                        'rule'        => null,
                    ],
                    'vitality'  => [
                        'type'        => 'simple',
                        'name'        => 'vitality',
                        'initial'     => 23,
                        'max'         => 23,
                        'value'       => 23,
                        'constraints' => [
                            'min' => 0,
                            'max' => 40,
                        ],
                        'rule'        => 'strength + endurance',
                    ],
                ],
                'compounds' => [
                    'attack'  => [
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
            'traits'  => [
                'skill'    => [],
                'state'    => [],
                'blessing' => [],
                'curse'    => [],
                'title'    => [],
            ],
            'inventory' => [
                'coins' => 10,
                'items' => [
                    [
                        'type'        => 'item',
                        'name'        => 'Rusty Sword',
                        'subType'     => 'sword',
                        'description' => 'A rusty sword',
                        'modifiers'   => [],
                        'flags'       => 6,
                        'equipped'    => false,
                        'damages'     => ['physical' => ['type' => 'physical', 'value' => 2]],
                        'quantity'    => 1,
                        'price'       => 0,
                    ],
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
            'name'      => 'Goblin #1',
            'type'      => 'creature',
            'info'      => [
                'level'       => 2,
                'xp'          => 200,
                'age'         => 20,
                'size'        => 'small',
                'race'        => 'goblin',
                'gender'      => 'unknown',
                'description' => 'An evil goblin',
                'background'  => 'Born in a small village',
                'notes'       => 'No special notes',
            ],
            'damages'   => ['physical' => ['type' => 'physical', 'value' => 2]],
            'attributes' => [
                'simples'     => [
                    'strength'  => [
                        'type'        => 'simple',
                        'name'        => 'strength',
                        'initial'     => 8,
                        'max'         => 8,
                        'value'       => 8,
                        'constraints' => [
                            'min' => 0,
                            'max' => 20,
                        ],
                        'rule'        => null,
                    ],
                    'endurance' => [
                        'type'        => 'simple',
                        'name'        => 'endurance',
                        'initial'     => 8,
                        'max'         => 8,
                        'value'       => 8,
                        'constraints' => [
                            'min' => 0,
                            'max' => 20,
                        ],
                        'rule'        => null,
                    ],
                    'agility'   => [
                        'type'        => 'simple',
                        'name'        => 'agility',
                        'initial'     => 14,
                        'max'         => 14,
                        'value'       => 14,
                        'constraints' => [
                            'min' => 0,
                            'max' => 20,
                        ],
                        'rule'        => null,
                    ],
                    'intuition' => [
                        'type'        => 'simple',
                        'name'        => 'intuition',
                        'initial'     => 10,
                        'max'         => 10,
                        'value'       => 10,
                        'constraints' => [
                            'min' => 0,
                            'max' => 20,
                        ],
                        'rule'        => null,
                    ],
                    'vitality'  => [
                        'type'        => 'simple',
                        'name'        => 'vitality',
                        'initial'     => 0,
                        'max'         => 0,
                        'value'       => 0,
                        'constraints' => [
                            'min' => 0,
                            'max' => 40,
                        ],
                        'rule'        => 'strength + endurance',
                    ],
                ],
                'compounds' => [
                    'attack'  => [
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
            'traits'  => [
                'skill'    => [],
                'state'    => [],
                'blessing' => [],
                'curse'    => [],
                'title'    => [],
            ],
            'inventory' => [
                'coins' => 2,
                'items' => [
                    [
                        'type'        => 'item',
                        'name'        => 'The Dagger',
                        'subType'     => 'dagger',
                        'description' => 'A sharp dagger',
                        'modifiers'   => [],
                        'flags'       => 7,
                        'equipped'    => true,
                        'damages'     => ['physical' => ['type' => 'physical', 'value' => 1]],
                        'quantity'    => 1,
                        'price'       => 0,
                    ],
                ],
            ],
        ];
    }
}
