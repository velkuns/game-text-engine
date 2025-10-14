<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Element\Factory;

use Velkuns\GameTextEngine\Element\Ability\BaseAbility;
use Velkuns\GameTextEngine\Element\Ability\ConstraintsAbility;
use Velkuns\GameTextEngine\Element\Condition\ConditionOperatorType;
use Velkuns\GameTextEngine\Element\Condition\ConditionType;
use Velkuns\GameTextEngine\Element\Entity\EntityType;
use Velkuns\GameTextEngine\Element\Exception\ElementJsonParseException;
use Velkuns\GameTextEngine\Element\Factory\AbilityFactory;
use Velkuns\GameTextEngine\Element\Factory\ConditionsFactory;
use Velkuns\GameTextEngine\Element\Factory\ElementFactory;
use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Element\Factory\EntityFactory;
use Velkuns\GameTextEngine\Element\Factory\ItemFactory;
use Velkuns\GameTextEngine\Element\Factory\ModifierFactory;
use Velkuns\GameTextEngine\Element\Factory\StatusFactory;
use Velkuns\GameTextEngine\Element\Status\StatusType;

class ElementFactoryTest extends TestCase
{
    private ElementFactory $elementFactory;

    public function setUp(): void
    {
        $abilityFactory    = new AbilityFactory();
        $modifierFactory   = new ModifierFactory();
        $conditionsFactory = new ConditionsFactory();
        $itemFactory       = new ItemFactory($modifierFactory);
        $statusFactory     = new StatusFactory($modifierFactory, $conditionsFactory);
        $entityFactory     = new EntityFactory($abilityFactory, $statusFactory, $itemFactory);

        $this->elementFactory = new ElementFactory(
            $entityFactory,
            $abilityFactory,
            $statusFactory,
            $itemFactory,
            $conditionsFactory,
            $modifierFactory,
        );
    }

    public function testStatusFromJson(): void
    {
        $json = '{
            "type": "skill",
            "name": "swordsmanship",
            "description": "Skill in using swords.",
            "modifiers": [
                {
                    "ability": "agility",
                    "value": 5
                },
                {
                    "ability": "attack",
                    "value": 10
                }
            ],
            "conditions": {
                "numberRequired": 1,
                "conditions": [
                    {
                        "type": "item",
                        "name": "",
                        "operator": "=",
                        "value": 1,
                        "subType": "sword",
                        "equipped": null,
                        "flags": null
                    }
                ]
            },
            "durationTurns": 0,
            "remainingTurns": 0
        }';

        $status = $this->elementFactory->statusFromJson($json);

        self::assertSame(StatusType::Skill, $status->getType());
        self::assertSame('swordsmanship', $status->getName());
        self::assertSame('Skill in using swords.', $status->getDescription());
        self::assertCount(2, $status->getModifiers());
        self::assertSame(0, $status->getDurationTurns());
        self::assertSame(0, $status->getRemainingTurns());
        self::assertNotNull($status->getConditions());
        self::assertSame(1, $status->getConditions()->getNumberRequired());
        self::assertCount(1, $status->getConditions()->getConditions());
    }

    public function testStatusFromJsonWithInvalidJson(): void
    {

        $json = '{
            "type": "skill",
            "name": "swordsmanship",
            "description": "Skill in using swords.",
            "modifiers": [
                {
                    "ability": "agility",
                    "value": 5
                },
                {
                    "ability": "attack",
                    "value": 10
                }
            ],
            "conditions": {
                "numberRequired": 1,
                "conditions": [
                    {
                        "type": "item",
                        "name": "",
                        "operator": "=",
                        "value": 1,
                        "subType": "sword",
                        "equipped": null,
                        "flags": null
                    }
                ]
            },
            "durationTurns": 0
            "remainingTurns": 0
        }';

        self::expectExceptionCode(2013);
        self::expectException(ElementJsonParseException::class);
        $this->elementFactory->statusFromJson($json);
    }

    public function testModifierFromJson(): void
    {
        $json = '{
            "ability": "strength",
            "value": 5
        }';

        $modifier = $this->elementFactory->modifierFromJson($json);

        self::assertSame('strength', $modifier->ability);
        self::assertSame(5, $modifier->value);
    }

    public function testModifierFromJsonWithInvalidJson(): void
    {
        $json = '{
            "ability": "strength",
            "value": 5
        ';

        self::expectExceptionCode(2014);
        self::expectException(ElementJsonParseException::class);
        $this->elementFactory->modifierFromJson($json);
    }

    public function testConditionsFromJson(): void
    {
        $json = '{
            "numberRequired": 1,
            "conditions": [
                {
                    "type": "item",
                    "name": "",
                    "operator": "=",
                    "value": 1,
                    "subType": "sword",
                    "equipped": null,
                    "flags": null
                }
            ]
        }';

        $conditions = $this->elementFactory->conditionsFromJson($json);
        self::assertSame(1, $conditions->getNumberRequired());
        self::assertCount(1, $conditions->getConditions());

        $condition = $conditions->getConditions()[0];
        self::assertSame(ConditionType::Item, $condition->getType());
        self::assertSame('', $condition->getName());
        self::assertSame(ConditionOperatorType::Equal, $condition->getOperator());
        self::assertSame(1, $condition->getValue());
        self::assertSame('sword', $condition->getSubType());
        self::assertNull($condition->isEquipped());
        self::assertNull($condition->getFlags());
    }

    public function testConditionsFromJsonWithInvalidJson(): void
    {
        $json = '{
            "numberRequired": 1,
            "conditions": [
                {
                    "type": "item",
                    "name": "",
                    "operator": "=",
                    "value": 1,
                    "subType": "sword",
                    "equipped": null,
                    "flags": null
                }
            ]
        ';

        self::expectExceptionCode(2015);
        self::expectException(ElementJsonParseException::class);
        $this->elementFactory->conditionsFromJson($json);
    }

    public function testConditionsFromJsonWithNullJson(): void
    {
        $json = 'null';

        self::expectExceptionCode(2016);
        self::expectException(ElementJsonParseException::class);
        $this->elementFactory->conditionsFromJson($json);
    }

    public function testAbilityBaseFromJson(): void
    {
        $json    = '{
            "type": "base",
            "name": "strength",
            "current": 10,
            "max": 20,
            "constraints": {
                "min": 0,
                "max": 100
            },
            "initial": 10,
            "rule": null
        }';
        $ability = $this->elementFactory->abilityBaseFromJson($json);

        self::assertSame('strength', $ability->getName());
        self::assertSame(10, $ability->getCurrent());
        self::assertSame(20, $ability->getMax());
        self::assertSame(10, $ability->getInitial());
        self::assertNull($ability->getRule());
        self::assertSame(0, $ability->getConstraints()->min);
        self::assertSame(100, $ability->getConstraints()->max);
        self::assertSame('base', $ability->getType()->value);
    }

    public function testAbilityBaseFromJsonWithInvalidJson(): void
    {
        $json = '{
            "type": "base",
            "name": "strength",
            "current": 10,
            "max": 20,
            "constraints": {
                "min": 0,
                "max": 100
            },
            "initial": 10,
            "rule": null
        ';

        self::expectExceptionCode(2011);
        self::expectException(ElementJsonParseException::class);
        $this->elementFactory->abilityBaseFromJson($json);
    }

    public function testAbilityCompoundFromJson(): void
    {
        $json    = '{
            "type": "compound",
            "name": "attack",
            "rule": "strength + agility"
        }';
        $bases = [
            'strength' => new BaseAbility('strength', 10, 20, new ConstraintsAbility(0, 100), 10),
            'agility'  => new BaseAbility('agility', 15, 30, new ConstraintsAbility(0, 100), 15),
        ];

        $ability = $this->elementFactory->abilityCompoundFromJson($json, $bases);

        self::assertSame('attack', $ability->getName());
        self::assertSame(25, $ability->getCurrent());
        self::assertSame(50, $ability->getMax());
        self::assertSame(25, $ability->getInitial());
        self::assertSame('strength + agility', $ability->getRule());
        self::assertSame(0, $ability->getConstraints()->min);
        self::assertSame(200, $ability->getConstraints()->max);
        self::assertSame('compound', $ability->getType()->value);
    }

    public function testAbilityCompoundFromJsonWithInvalidJson(): void
    {
        $json    = '{
            "type": "compound",
            "name": "attack",
            "rule": "strength + agility"
        ';
        $bases = [];

        self::expectExceptionCode(2012);
        self::expectException(ElementJsonParseException::class);
        $this->elementFactory->abilityCompoundFromJson($json, $bases);
    }

    public function testItemFromJson(): void
    {
        $json = '{
            "type": "item",
            "name": "The Sword",
            "subType": "sword",
            "description": "A sharp blade.",
            "modifiers": [
                {
                    "ability": "attack",
                    "value": 10
                }
            ],
            "flags": 1,
            "equipped": false,
            "damages": 2,
            "price": 100
        }';

        $item = $this->elementFactory->itemFromJson($json);

        self::assertSame('The Sword', $item->getName());
        self::assertSame('sword', $item->getSubType());
        self::assertSame("A sharp blade.", $item->getDescription());
        self::assertSame(1, $item->getFlags());
        self::assertFalse($item->isEquipped());
        self::assertSame(2, $item->getDamages());
        self::assertSame(100, $item->getPrice());
        self::assertSame('item', $item->getType());
        self::assertCount(1, $item->getModifiers());
    }

    public function testItemFromJsonWithInvalidJson(): void
    {
        $json = '{
            "type": "item",
            "name": "The Sword",
            "subType": "sword",
            "description": "A sharp blade.",
            "modifiers": [
                {
                    "ability": "attack",
                    "value": 10
                }
            ],
            "flags": 1,
            "equipped": false,
            "damages": 2,
            "price": 100
        ';

        self::expectExceptionCode(2016);
        self::expectException(ElementJsonParseException::class);
        $this->elementFactory->itemFromJson($json);
    }

    public function testEntityFromJson(): void
    {
        $json = '{
            "name": "Brave Test Hero #1",
            "type": "player",
            "coins": 100,
            "info": {
                "level": 5,
                "age": 30,
                "size": "1m75",
                "race": "elf",
                "description": "A brave hero",
                "background": "Born in a small village",
                "notes": "No special notes"
            },
            "abilities": {
                "bases": {
                    "strength": {
                        "type": "base",
                        "name": "strength",
                        "current": 10,
                        "max": 20,
                        "constraints": {
                            "min": 0,
                            "max": 100
                        },
                        "initial": 10,
                        "rule": null
                    },
                    "agility": {
                        "type": "base",
                        "name": "agility",
                        "current": 15,
                        "max": 30,
                        "constraints": {
                            "min": 0,
                            "max": 100
                        },
                        "initial": 15,
                        "rule": null
                    },
                    "endurance": {
                        "type": "base",
                        "name": "endurance",
                        "current": 12,
                        "max": 25,
                        "constraints": {
                            "min": 0,
                            "max": 100
                        },
                        "initial": 12,
                        "rule": null
                    },
                    "intuition": {
                        "type": "base",
                        "name": "intuition",
                        "current": 8,
                        "max": 20,
                        "constraints": {
                            "min": 0,
                            "max": 100
                        },
                        "initial": 8,
                        "rule": null
                    }
                },        
                "compounds": {
                    "attack": {
                        "type": "compound",
                        "name": "attack",
                        "rule": "strength + agility"
                    },
                    "defense": {
                        "type": "compound",
                        "name": "defense",
                        "rule": "endurance + intuition"
                    }
                }
            },
            "statuses": {
                "skills": {
                    "swordsmanship": {
                        "type": "skill",
                        "name": "swordsmanship",
                        "description": "Super skill",
                        "modifiers": [
                            {
                                "ability": "agility",
                                "value": 5
                            },
                            {
                                "ability": "attack",
                                "value": 10
                            }
                        ],
                        "conditions": {
                            "numberRequired": 1,
                            "conditions": [
                                {
                                    "type": "item",
                                    "name": "",
                                    "operator": "=",
                                    "value": 1,
                                    "subType": "sword",
                                    "equipped": true,
                                    "flags": 3
                                }
                            ]
                        },
                        "durationTurns": 0,
                        "remainingTurns": 0
                    }
                },
                "states": [],
                "blessings": [],
                "curses": [],
                "titles": []
            },
            "inventory": [
                {
                    "type": "item",
                    "name": "The Sword",
                    "subType": "sword",
                    "description": "A sharp blade",
                    "modifiers": [],
                    "flags": 7,
                    "equipped": true,
                    "damages": 2,
                    "price": 0
                }
            ]
        }';

        $hero = $this->elementFactory->entityFromJson($json);

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
        self::assertSame(2, $item->getDamages());
        self::assertSame(7, $item->getFlags());
        self::assertSame(0, $item->getPrice());
        self::assertEmpty($item->getModifiers());
        self::assertSame('item', $item->getType());
        self::assertTrue($item->isConsumable());
        self::assertTrue($item->isEquipped());
        self::assertFalse($item->isGear());
        self::assertTrue($item->isEquipable());
        self::assertTrue($item->isWeapon());
    }

    public function testEntityFromJsonWithInvalidJson(): void
    {
        $json = '{
            "name": "Brave Test Hero #1",
            "type": "player",
        ';

        self::expectExceptionCode(2010);
        self::expectException(ElementJsonParseException::class);
        $this->elementFactory->entityFromJson($json);
    }
}
