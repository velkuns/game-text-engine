<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Element\Factory;

use Velkuns\GameTextEngine\Element\Entity\Entity;
use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Entity\EntityType;
use Velkuns\GameTextEngine\Element\Factory\AbilityFactory;
use Velkuns\GameTextEngine\Element\Factory\ConditionsFactory;
use Velkuns\GameTextEngine\Element\Factory\EntityFactory;
use Velkuns\GameTextEngine\Element\Factory\ItemFactory;
use Velkuns\GameTextEngine\Element\Factory\ModifierFactory;
use Velkuns\GameTextEngine\Element\Factory\StatusFactory;
use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Element\Status\StatusType;

/**
 * @phpstan-import-type EntityData from EntityInterface
 */
class EntityFactoryTest extends TestCase
{
    private EntityFactory $entityFactory;

    public function setUp(): void
    {
        $this->entityFactory    = new EntityFactory(
            new AbilityFactory(),
            new StatusFactory(new ModifierFactory(), new ConditionsFactory()),
            new ItemFactory(new ModifierFactory()),
        );
    }

    public function testFrom(): void
    {
        $data = self::getHeroData();
        $hero = $this->entityFactory->from($data);

        self::assertSame('Brave Test Hero #1', $hero->getName());
        self::assertSame(EntityType::Player, $hero->getType());
        self::assertSame(100, $hero->getCoins());

        $info = $hero->getInfo();
        self::assertSame(5, $info->level);
        self::assertSame(30, $info->age);
        self::assertSame('1m75', $info->size);
        self::assertSame('elf', $info->race);
        self::assertSame('A brave hero', $info->description);
        self::assertSame('Born in a small village', $info->background);
        self::assertSame('No special notes', $info->notes);

        $ability = $hero->getAbilities()->get('strength');
        self::assertNotNull($ability);
        self::assertSame(10, $ability->getCurrent());
        self::assertSame(20, $ability->getMax());
        self::assertSame(10, $ability->getInitial());
        self::assertNull($ability->getRule());
        self::assertSame('strength', $ability->getName());

        $statuses = $hero->getStatuses();
        self::assertCount(1, $statuses->skills);
        self::assertCount(0, $statuses->states);
        self::assertCount(0, $statuses->blessings);
        self::assertCount(0, $statuses->curses);
        self::assertCount(0, $statuses->titles);

        self::assertFalse($hero->hasStatus(StatusType::Skill, 'non-existing-skill'));
        self::assertFalse($hero->hasStatus(StatusType::State, 'non-existing-skill'));
        self::assertFalse($hero->hasStatus(StatusType::Blessing, 'non-existing-skill'));
        self::assertFalse($hero->hasStatus(StatusType::Curse, 'non-existing-skill'));
        self::assertFalse($hero->hasStatus(StatusType::Title, 'non-existing-skill'));


        $item = $hero->getInventory()->get('The Sword');
        self::assertNotNull($item);
        self::assertSame('The Sword', $item->getName());
        self::assertSame('sword', $item->getSubType());
        self::assertSame('A sharp blade', $item->getDescription());
        self::assertTrue($item->isEquipped());
        self::assertSame(7, $item->getFlags());
        self::assertSame(0, $item->getPrice());
        self::assertEmpty($item->getModifiers());
        self::assertSame('item', $item->getType());
        self::assertTrue($item->isConsumable());
        self::assertTrue($item->isEquipped());
        self::assertFalse($item->isGear());
        self::assertTrue($item->isEquipable());
        self::assertTrue($item->isWeapon());

        self::assertSame($data, $hero->jsonSerialize());

        self::assertNull($hero->getInventory()->get('The Axe'));

        $axe = (new ItemFactory(new ModifierFactory()))->from([
            'type'        => 'item',
            'name'        => 'The Axe',
            'subType'     => 'axe',
            'description' => 'A sharp axe',
            'modifiers'   => [],
            'flags'       => 7,
            'equipped'    => false,
            'price'       => 10,
        ]);
        $hero->getInventory()->add($axe);
        self::assertNotNull($hero->getInventory()->get('The Axe'));

        $hero->getInventory()->drop($axe);
        self::assertNull($hero->getInventory()->get('The Axe'));
    }

    /**
     * @return EntityData
     */
    public static function getHeroData(): array
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
                    'price'       => 0,
                ],
            ],
        ];
    }
}
