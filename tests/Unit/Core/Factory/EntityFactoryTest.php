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
use Velkuns\GameTextEngine\Rpg\Damages\DamagesDetail;
use Velkuns\GameTextEngine\Rpg\Damages\Damages;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;

/**
 * @phpstan-import-type EntityData from EntityInterface
 */
class EntityFactoryTest extends TestCase
{
    use EntityTrait;
    use FactoryTrait;

    public function testFrom(): void
    {
        $data = self::getPlayerData();
        $hero = self::getEntityFactory()->from($data);

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
        self::assertSame(10, $attribute->getMax());
        self::assertSame(10, $attribute->getInitial());
        self::assertNull($attribute->getRule());
        self::assertSame('strength', $attribute->getName());

        $traits = $hero->getTraits();
        self::assertCount(0, $traits->traits['race']);
        self::assertCount(0, $traits->traits['class']);
        self::assertCount(3, $traits->traits['skill']);
        self::assertCount(1, $traits->traits['title']);

        $alterations = $hero->getAlterations();
        self::assertCount(1, $alterations->alterations['state']);
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
        self::assertSame(6, $item->getFlags());
        self::assertSame(0, $item->getPrice());
        self::assertEmpty($item->getModifiers());
        self::assertSame('item', $item->getType());
        self::assertFalse($item->isConsumable());
        self::assertTrue($item->equipped());
        self::assertFalse($item->isGear());
        self::assertTrue($item->isEquipable());
        self::assertTrue($item->isWeapon());

        self::assertSame($data, $hero->jsonSerialize());

        self::assertNull($hero->getInventory()->get('The Bow'));

        $axe = self::getItemFactory()->from([
            'type'        => 'item',
            'name'        => 'The Bow',
            'subType'     => 'bow',
            'description' => 'A short bow',
            'modifiers'   => [],
            'flags'       => 6,
            'equipped'    => false,
            'damages'     => ['physical' => ['type' => 'physical', 'value' => 2]],
            'price'       => 10,
        ]);
        $hero->getInventory()->add($axe);
        self::assertNotNull($hero->getInventory()->get('The Bow'));

        $hero->getInventory()->drop($axe);
        self::assertNull($hero->getInventory()->get('The Bow'));
    }
}
