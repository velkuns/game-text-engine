<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Element\Factory;

use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Factory\ItemFactory;
use Velkuns\GameTextEngine\Element\Factory\ModifierFactory;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;

/**
 * @phpstan-import-type EntityData from EntityInterface
 */
class EntityFactoryTest extends TestCase
{
    use EntityTrait;

    public function testFrom(): void
    {
        $data = self::getPlayerData();
        $hero = self::getEntityFactory()->from($data);

        self::assertSame('Brave Test Hero #1', $hero->getName());
        self::assertSame('player', $hero->getType());
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
        self::assertSame(10, $ability->getValue());
        self::assertSame(10, $ability->getMax());
        self::assertSame(10, $ability->getInitial());
        self::assertNull($ability->getRule());
        self::assertSame('strength', $ability->getName());

        $statuses = $hero->getStatuses();
        self::assertCount(2, $statuses->skills);
        self::assertCount(1, $statuses->states);
        self::assertCount(0, $statuses->blessings);
        self::assertCount(0, $statuses->curses);
        self::assertCount(1, $statuses->titles);

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
        self::assertSame(2, $item->getDamages());
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

        $axe = (new ItemFactory(new ModifierFactory()))->from([
            'type'        => 'item',
            'name'        => 'The Bow',
            'subType'     => 'bow',
            'description' => 'A short bow',
            'modifiers'   => [],
            'flags'       => 6,
            'equipped'    => false,
            'damages'     => 2,
            'price'       => 10,
        ]);
        $hero->getInventory()->add($axe);
        self::assertNotNull($hero->getInventory()->get('The Bow'));

        $hero->getInventory()->drop($axe);
        self::assertNull($hero->getInventory()->get('The Bow'));
    }
}
