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
use Velkuns\GameTextEngine\Rpg\Attribute\SimpleAttribute;
use Velkuns\GameTextEngine\Rpg\Attribute\ConstraintsAttribute;
use Velkuns\GameTextEngine\Rpg\Damages\Damages;
use Velkuns\GameTextEngine\Rpg\Damages\DamagesDetail;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;

class ElementFactoryTest extends TestCase
{
    use FactoryTrait;

    public function testTraitFromJson(): void
    {
        $json = '{
            "type": "skill",
            "name": "swordsmanship",
            "description": "Skill in using swords.",
            "modifiers": [
                {
                    "type": "self.attribute.agility.value",
                    "value": 5,
                    "prerequisites": {
                        "numberRequired": 1,
                        "requirements": [
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
                    "prerequisites": {
                        "numberRequired": 1,
                        "requirements": [
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

        $trait    = self::getElementFactory()->traitFromJson($json);
        $modifiers = $trait->getModifiers();

        self::assertSame('skill', $trait->getType());
        self::assertSame('swordsmanship', $trait->getName());
        self::assertSame('Skill in using swords.', $trait->getDescription());
        self::assertCount(2, $modifiers);
        self::assertNotNull($modifiers[0]->prerequisites);
        self::assertSame(1, $modifiers[0]->prerequisites->getNumberRequired());
        self::assertCount(1, $modifiers[0]->prerequisites->getRequirements());
    }

    public function testTraitFromJsonWithInvalidJson(): void
    {

        $json = '{
            "type": "skill",
            "name": "swordsmanship",
            "description": "Skill in using swords.",
            "modifiers": [
                {
                    "type": "self.attribute.agility.value",
                    "value": 5,
                    "prerequisites": {
                        "numberRequired": 1,
                        "requirements": [
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
                    "prerequisites": {
                        "numberRequired": 1,
                        "requirements": [
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
        self::getElementFactory()->traitFromJson($json);
    }

    public function testAlterationFromJson(): void
    {
        $json = '{
            "type": "state",
            "name": "Poisoned",
            "description": "You are poisoned for 3 turn.",
            "modifiers": [
                {
                    "type": "self.attribute.agility.vitality",
                    "value": -2
                }
            ],
            "duration": {
                "max": 3,
                "remaining": 3
            }
        }';

        $alteration = self::getElementFactory()->alterationFromJson($json);
        $modifiers  = $alteration->getModifiers();

        self::assertSame('state', $alteration->getType());
        self::assertSame('Poisoned', $alteration->getName());
        self::assertSame('You are poisoned for 3 turn.', $alteration->getDescription());
        self::assertCount(1, $modifiers);
        self::assertSame(3, $alteration->getDuration()->getMax());
        self::assertSame(3, $alteration->getDuration()->getRemaining());
        self::assertNull($modifiers[0]->prerequisites);
    }

    public function testAlterationFromJsonWithInvalidJson(): void
    {

        $json = '{
            "type": "state",
            "name": "Poisoned",
            "description": "You are poisoned for 3 turn.",
            "modifiers": [
                {
                    "type": "self.attribute.agility.vitality",
                    "value": -2
                }
            ],
            "durationTurns": 3
            "remainingTurns": 3
        }';

        self::expectExceptionCode(2018);
        self::expectException(ElementJsonParseException::class);
        self::getElementFactory()->alterationFromJson($json);
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
            "requirements": [
                {
                        "type": "self.inventory.item",
                        "condition": "subType=sword",
                        "is": true
                }
            ]
        }';

        $conditions = self::getElementFactory()->prerequisitesFromJson($json);
        self::assertSame(1, $conditions->getNumberRequired());
        self::assertCount(1, $conditions->getRequirements());

        $condition = $conditions->getRequirements()[0];
        self::assertSame('self.inventory.item', $condition->getType());
        self::assertSame('subType=sword', $condition->getCondition());
        self::assertTrue($condition->is());
    }

    public function testConditionsFromJsonWithInvalidJson(): void
    {
        $json = '{
            "numberRequired": 1,
            "requirements": [
                {
                        "type": "self.inventory.item",
                        "condition": "subType=sword",
                        "is": true
                }
            ]
        ';

        self::expectExceptionCode(2015);
        self::expectException(ElementJsonParseException::class);
        self::getElementFactory()->prerequisitesFromJson($json);
    }

    public function testConditionsFromJsonWithNullJson(): void
    {
        $json = 'null';

        self::expectExceptionCode(2016);
        self::expectException(ElementJsonParseException::class);
        self::getElementFactory()->prerequisitesFromJson($json);
    }

    public function testAttributeSimpleFromJson(): void
    {
        $json    = '{
            "type": "simple",
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
        $attribute = self::getElementFactory()->attributeSimpleFromJson($json);

        self::assertSame('strength', $attribute->getName());
        self::assertSame(10, $attribute->getValue());
        self::assertSame(20, $attribute->getMax());
        self::assertSame(10, $attribute->getInitial());
        self::assertNull($attribute->getRule());
        self::assertSame(0, $attribute->getConstraints()->min);
        self::assertSame(100, $attribute->getConstraints()->max);
        self::assertSame('simple', $attribute->getType()->value);
    }

    public function testAttributeSimpleFromJsonWithInvalidJson(): void
    {
        $json = '{
            "type": "simple",
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
        self::getElementFactory()->attributeSimpleFromJson($json);
    }

    public function testAttributeCompoundFromJson(): void
    {
        $json    = '{
            "type": "compound",
            "name": "attack",
            "rule": "strength + agility"
        }';
        $simples = [
            'strength' => new SimpleAttribute('strength', 10, 20, new ConstraintsAttribute(0, 100), 10),
            'agility'  => new SimpleAttribute('agility', 15, 30, new ConstraintsAttribute(0, 100), 15),
        ];

        $attribute = self::getElementFactory()->attributeCompoundFromJson($json, $simples);

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
        $simples = [];

        self::expectExceptionCode(2012);
        self::expectException(ElementJsonParseException::class);
        self::getElementFactory()->attributeCompoundFromJson($json, $simples);
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
                "simples": {
                    "strength": {
                        "type": "simple",
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
                        "type": "simple",
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
                        "type": "simple",
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
                        "type": "simple",
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
            "traits": {
                "race": [],
                "class": [],
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
                        "prerequisites": {
                            "numberRequired": 1,
                            "requirements": [
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
                "title": []
            },
            "alterations": {
                "state": [],
                "blessing": [],
                "curse": []
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

        $traits = $hero->getTraits();
        self::assertCount(0, $traits->traits['race']);
        self::assertCount(0, $traits->traits['class']);
        self::assertCount(1, $traits->traits['skill']);
        self::assertCount(0, $traits->traits['title']);

        $alterations = $hero->getAlterations();
        self::assertCount(0, $alterations->alterations['state']);
        self::assertCount(0, $alterations->alterations['blessing']);
        self::assertCount(0, $alterations->alterations['curse']);

        self::assertFalse($hero->hasTrait('race', 'non-existing-race'));
        self::assertFalse($hero->hasTrait('class', 'non-existing-class'));
        self::assertFalse($hero->hasTrait('skill', 'non-existing-skill'));
        self::assertFalse($hero->hasTrait('title', 'non-existing-title'));

        self::assertFalse($hero->hasAlteration('state', 'non-existing-state'));
        self::assertFalse($hero->hasAlteration('blessing', 'non-existing-blessing'));
        self::assertFalse($hero->hasAlteration('curse', 'non-existing-curse'));


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
