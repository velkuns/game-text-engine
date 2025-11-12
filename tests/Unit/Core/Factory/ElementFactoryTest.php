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
use Velkuns\GameTextEngine\Exception\Core\ElementJsonParseException;
use Velkuns\GameTextEngine\Rpg\Attribute\BaseAttribute;
use Velkuns\GameTextEngine\Rpg\Attribute\ConstraintsAttribute;
use Velkuns\GameTextEngine\Rpg\Damages\Damages;
use Velkuns\GameTextEngine\Rpg\Damages\DamagesDetail;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;

class ElementFactoryTest extends TestCase
{
    use FactoryTrait;

    public function testStatusFromJson(): void
    {
        $json = '{
            "type": "skill",
            "name": "swordsmanship",
            "description": "Skill in using swords.",
            "modifiers": [
                {
                    "type": "self.attribute.agility.value",
                    "value": 5,
                    "conditions": {
                        "numberRequired": 1,
                        "conditions": [
                            {
                                "type": "self.inventory.item",
                                "condition": "subType=sword",
                                "is": true
                            }
                        ]
                    }
                },
                {
                    "type": "self.attribute.attack.value",
                    "value": 10,
                    "conditions": {
                        "numberRequired": 1,
                        "conditions": [
                            {
                                "type": "self.inventory.item",
                                "condition": "subType=sword",
                                "is": true
                            }
                        ]
                    }
                }
            ]
        }';

        $status    = self::getElementFactory()->statusFromJson($json);
        $modifiers = $status->getModifiers();

        self::assertSame('skill', $status->getType());
        self::assertSame('swordsmanship', $status->getName());
        self::assertSame('Skill in using swords.', $status->getDescription());
        self::assertCount(2, $modifiers);
        self::assertSame(0, $status->getDurationTurns());
        self::assertSame(0, $status->getRemainingTurns());
        self::assertNotNull($modifiers[0]->conditions);
        self::assertSame(1, $modifiers[0]->conditions->getNumberRequired());
        self::assertCount(1, $modifiers[0]->conditions->getConditions());
    }

    public function testStatusFromJsonWithInvalidJson(): void
    {

        $json = '{
            "type": "skill",
            "name": "swordsmanship",
            "description": "Skill in using swords.",
            "modifiers": [
                {
                    "type": "self.attribute.agility.value",
                    "value": 5,
                    "conditions": {
                        "numberRequired": 1,
                        "conditions": [
                            {
                                "type": "self.inventory.item",
                                "condition": "subType=sword",
                                "is": true
                            }
                        ]
                    }
                },
                {
                    "type": "self.attribute.attack.value",
                    "value": 10,
                    "conditions": {
                        "numberRequired": 1,
                        "conditions": [
                            {
                                "type": "self.inventory.item",
                                "condition": "subType=sword",
                                "is": true
                            }
                        ]
                    }
                },
            ]
        }';

        self::expectExceptionCode(2013);
        self::expectException(ElementJsonParseException::class);
        self::getElementFactory()->statusFromJson($json);
    }

    public function testModifierFromJson(): void
    {
        $json = '{
            "type": "self.attribute.strength.value",
            "value": 5
        }';

        $modifier = self::getElementFactory()->modifierFromJson($json);

        self::assertSame('self.attribute.strength.value', $modifier->type);
        self::assertSame(5, $modifier->value);
    }

    public function testModifierFromJsonWithInvalidJson(): void
    {
        $json = '{
            "type": "self.attribute.strength.value",
            "value": 5
        ';

        self::expectExceptionCode(2014);
        self::expectException(ElementJsonParseException::class);
        self::getElementFactory()->modifierFromJson($json);
    }

    public function testConditionsFromJson(): void
    {
        $json = '{
            "numberRequired": 1,
            "conditions": [
                {
                        "type": "self.inventory.item",
                        "condition": "subType=sword",
                        "is": true
                }
            ]
        }';

        $conditions = self::getElementFactory()->conditionsFromJson($json);
        self::assertSame(1, $conditions->getNumberRequired());
        self::assertCount(1, $conditions->getConditions());

        $condition = $conditions->getConditions()[0];
        self::assertSame('self.inventory.item', $condition->getType());
        self::assertSame('subType=sword', $condition->getCondition());
        self::assertTrue($condition->is());
    }

    public function testConditionsFromJsonWithInvalidJson(): void
    {
        $json = '{
            "numberRequired": 1,
            "conditions": [
                {
                        "type": "self.inventory.item",
                        "condition": "subType=sword",
                        "is": true
                }
            ]
        ';

        self::expectExceptionCode(2015);
        self::expectException(ElementJsonParseException::class);
        self::getElementFactory()->conditionsFromJson($json);
    }

    public function testConditionsFromJsonWithNullJson(): void
    {
        $json = 'null';

        self::expectExceptionCode(2016);
        self::expectException(ElementJsonParseException::class);
        self::getElementFactory()->conditionsFromJson($json);
    }

    public function testAttributeBaseFromJson(): void
    {
        $json    = '{
            "type": "base",
            "name": "strength",
            "value": 10,
            "max": 20,
            "constraints": {
                "min": 0,
                "max": 100
            },
            "initial": 10,
            "rule": null
        }';
        $attribute = self::getElementFactory()->attributeBaseFromJson($json);

        self::assertSame('strength', $attribute->getName());
        self::assertSame(10, $attribute->getValue());
        self::assertSame(20, $attribute->getMax());
        self::assertSame(10, $attribute->getInitial());
        self::assertNull($attribute->getRule());
        self::assertSame(0, $attribute->getConstraints()->min);
        self::assertSame(100, $attribute->getConstraints()->max);
        self::assertSame('base', $attribute->getType()->value);
    }

    public function testAttributeBaseFromJsonWithInvalidJson(): void
    {
        $json = '{
            "type": "base",
            "name": "strength",
            "value": 10,
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
        self::getElementFactory()->attributeBaseFromJson($json);
    }

    public function testAttributeCompoundFromJson(): void
    {
        $json    = '{
            "type": "compound",
            "name": "attack",
            "rule": "strength + agility"
        }';
        $bases = [
            'strength' => new BaseAttribute('strength', 10, 20, new ConstraintsAttribute(0, 100), 10),
            'agility'  => new BaseAttribute('agility', 15, 30, new ConstraintsAttribute(0, 100), 15),
        ];

        $attribute = self::getElementFactory()->attributeCompoundFromJson($json, $bases);

        self::assertSame('attack', $attribute->getName());
        self::assertSame(25, $attribute->getValue());
        self::assertSame(50, $attribute->getMax());
        self::assertSame(25, $attribute->getInitial());
        self::assertSame('strength + agility', $attribute->getRule());
        self::assertSame(0, $attribute->getConstraints()->min);
        self::assertSame(200, $attribute->getConstraints()->max);
        self::assertSame('compound', $attribute->getType()->value);
    }

    public function testAttributeCompoundFromJsonWithInvalidJson(): void
    {
        $json    = '{
            "type": "compound",
            "name": "attack",
            "rule": "strength + agility"
        ';
        $bases = [];

        self::expectExceptionCode(2012);
        self::expectException(ElementJsonParseException::class);
        self::getElementFactory()->attributeCompoundFromJson($json, $bases);
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
                    "type": "self.attribute.attack.value",
                    "value": 10
                }
            ],
            "flags": 1,
            "equipped": false,
            "damages": {
                "physical": {
                    "type": "physical",
                    "value": 2
                }
            },
            "price": 100
        }';

        $item = self::getElementFactory()->itemFromJson($json);

        self::assertSame('The Sword', $item->getName());
        self::assertSame('sword', $item->getSubType());
        self::assertSame("A sharp blade.", $item->getDescription());
        self::assertSame(1, $item->getFlags());
        self::assertFalse($item->equipped());
        self::assertEquals(new Damages(['physical' => new DamagesDetail('physical', 2)]), $item->getDamages());
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
                    "type": "self.attribute.attack.value",
                    "value": 10
                }
            ],
            "flags": 1,
            "equipped": false,
            "damages": {
                "physical": {
                    "type": "physical",
                    "value": 2
                }
            },
            "price": 100
        ';

        self::expectExceptionCode(2016);
        self::expectException(ElementJsonParseException::class);
        self::getElementFactory()->itemFromJson($json);
    }

    public function testEntityFromJson(): void
    {
        $json = '{
            "name": "Brave Test Hero #1",
            "type": "player",
            "info": {
                "level": 5,
                "xp": 1500,
                "age": 30,
                "size": "1m75",
                "race": "elf",
                "gender": "non-binary",
                "description": "A brave hero",
                "background": "Born in a small village",
                "notes": "No special notes"
            },
            "damages": {
                "physical": {
                    "type": "physical",
                    "value": 0
                }
            },
            "attributes": {
                "bases": {
                    "strength": {
                        "type": "base",
                        "name": "strength",
                        "value": 10,
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
                        "value": 15,
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
                        "value": 12,
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
                        "value": 8,
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
                "skill": {
                    "swordsmanship": {
                        "type": "skill",
                        "name": "swordsmanship",
                        "description": "Super skill",
                        "modifiers": [
                            {
                                "type": "self.attribute.agility.value",
                                "value": 5
                            },
                            {
                                "type": "self.attack.value",
                                "value": 10
                            }
                        ],
                        "conditions": {
                            "numberRequired": 1,
                            "conditions": [
                                {
                                    "type": "self.inventory.item",
                                    "condition": "subType=sword;equipped=true;flags&3",
                                    "is": true
                                }
                            ]
                        },
                        "durationTurns": 0,
                        "remainingTurns": 0
                    }
                },
                "state": [],
                "blessing": [],
                "curse": [],
                "title": []
            },
            "inventory": {
                "coins": 100,
                "items": [
                    {
                        "type": "item",
                        "name": "The Sword",
                        "subType": "sword",
                        "description": "A sharp blade",
                        "modifiers": [],
                        "flags": 7,
                        "equipped": true,
                        "damages": {
                            "physical": {
                                "type": "physical",
                                "value": 2
                            }
                        },
                        "price": 0
                    }
                ]
            }
        }';

        $hero = self::getElementFactory()->entityFromJson($json);

        self::assertSame('Brave Test Hero #1', $hero->getName());
        self::assertSame('player', $hero->getType());
        self::assertSame(100, $hero->getInventory()->coins);

        $info = $hero->getInfo();
        self::assertSame(5, $info->level);
        self::assertSame(30, $info->age);
        self::assertSame('1m75', $info->size);
        self::assertSame('elf', $info->race);
        self::assertSame('A brave hero', $info->description);
        self::assertSame('Born in a small village', $info->background);
        self::assertSame('No special notes', $info->notes);

        $attribute = $hero->getAttributes()->get('strength');
        self::assertNotNull($attribute);
        self::assertSame(10, $attribute->getValue());
        self::assertSame(20, $attribute->getMax());
        self::assertSame(10, $attribute->getInitial());
        self::assertNull($attribute->getRule());
        self::assertSame('strength', $attribute->getName());

        $statuses = $hero->getStatuses();
        self::assertCount(1, $statuses->statuses['skill']);
        self::assertCount(0, $statuses->statuses['state']);
        self::assertCount(0, $statuses->statuses['blessing']);
        self::assertCount(0, $statuses->statuses['curse']);
        self::assertCount(0, $statuses->statuses['title']);

        self::assertFalse($hero->hasStatus('skill', 'non-existing-skill'));
        self::assertFalse($hero->hasStatus('state', 'non-existing-skill'));
        self::assertFalse($hero->hasStatus('blessing', 'non-existing-skill'));
        self::assertFalse($hero->hasStatus('curse', 'non-existing-skill'));
        self::assertFalse($hero->hasStatus('title', 'non-existing-skill'));


        $item = $hero->getInventory()->get('The Sword');
        self::assertNotNull($item);
        self::assertSame('The Sword', $item->getName());
        self::assertSame('sword', $item->getSubType());
        self::assertSame('A sharp blade', $item->getDescription());
        self::assertTrue($item->equipped());
        self::assertEquals(new Damages(['physical' => new DamagesDetail('physical', 2)]), $item->getDamages());
        self::assertSame(7, $item->getFlags());
        self::assertSame(0, $item->getPrice());
        self::assertEmpty($item->getModifiers());
        self::assertSame('item', $item->getType());
        self::assertTrue($item->isConsumable());
        self::assertTrue($item->equipped());
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
        self::getElementFactory()->entityFromJson($json);
    }
}
