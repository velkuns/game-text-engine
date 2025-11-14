<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Integration\Api;

use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Exception\Api\BestiaryApiException;
use Velkuns\GameTextEngine\Tests\Helper\ApiTrait;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;

class BestiaryApiTest extends TestCase
{
    use ApiTrait;
    use FactoryTrait;

    public function testLoad(): void
    {
        $items    = self::getItemsApi();
        $bestiary = self::getBestiaryApi();

        $entity = $bestiary->get('Goblin');

        self::assertSame('Goblin', $entity->getName());
        self::assertSame('creature', $entity->getType());
        self::assertEquals($items->get('Rusty Dagger'), $entity->getInventory()->get('Rusty Dagger'));
    }
    public function testAddAndRemoveEntity(): void
    {
        $bestiary = self::getBestiaryApi();

        //~ Get existing entity, transform to data, modify name & strength, create new entity and add it to bestiary
        $entity = $bestiary->get('Goblin');

        $entityData = $entity->jsonSerialize();
        $entityData['name'] = 'Goblin Warrior';

        self::assertArrayHasKey('strength', $entityData['attributes']['simples']);

        $entityData['attributes']['simples']['strength']['value'] = 20;

        $goblinWarrior = self::getEntityFactory()->from($entityData);

        $bestiary->set($goblinWarrior);

        $goblinWarriorFromBestiary = $bestiary->get('Goblin Warrior', false);

        self::assertSame($goblinWarrior, $goblinWarriorFromBestiary); // get as no clone
        self::assertSame(20, $goblinWarriorFromBestiary->getAttributes()->get('strength')?->getValue());

        //~ Now remove Goblin Warrior entity and check it is removed
        $bestiary->remove($goblinWarriorFromBestiary->getName());
        self::expectException(BestiaryApiException::class);
        self::expectExceptionCode(1701);
        $bestiary->get('Goblin Warrior');
    }

    public function testLoadWhenThrowException(): void
    {
        $bestiary = self::getBestiaryApi();

        self::expectException(BestiaryApiException::class);
        self::expectExceptionMessage("Entity 'King Goblin' not found in bestiary.");
        $bestiary->get('King Goblin');
    }

    public function testGetWithPositiveAutoLevel(): void
    {
        $bestiary = self::getBestiaryApi();

        $goblin = $bestiary->get('Goblin');
        $betterGoblin = $bestiary->get('Goblin', autoLevel: 2);
        $lesserGoblin = $bestiary->get('Goblin', autoLevel: -2);

        $betterExpected = [
            'strength'  => ($goblin->getAttributes()->get('strength')?->getValue() ?? 0) + 2,
            'endurance' => ($goblin->getAttributes()->get('endurance')?->getValue() ?? 0) + 2,
            'agility'   => ($goblin->getAttributes()->get('agility')?->getValue() ?? 0) + 2,
            'intuition' => ($goblin->getAttributes()->get('intuition')?->getValue() ?? 0) + 2,
            'vitality'  => ($goblin->getAttributes()->get('vitality')?->getValue() ?? 0) + 6,
            'attack'    => ($goblin->getAttributes()->get('attack')?->getValue() ?? 0) + 4,
            'defense'   => ($goblin->getAttributes()->get('defense')?->getValue() ?? 0) + 4,
        ];

        $betterAutoLeveling = [
            'strength'  => ($betterGoblin->getAttributes()->get('strength')?->getValue() ?? 0),
            'endurance' => ($betterGoblin->getAttributes()->get('endurance')?->getValue() ?? 0),
            'agility'   => ($betterGoblin->getAttributes()->get('agility')?->getValue() ?? 0),
            'intuition' => ($betterGoblin->getAttributes()->get('intuition')?->getValue() ?? 0),
            'vitality'  => ($betterGoblin->getAttributes()->get('vitality')?->getValue() ?? 0),
            'attack'    => ($betterGoblin->getAttributes()->get('attack')?->getValue() ?? 0),
            'defense'   => ($betterGoblin->getAttributes()->get('defense')?->getValue() ?? 0),
        ];

        $lesserExpected = [
            'strength'  => ($goblin->getAttributes()->get('strength')?->getValue() ?? 0) - 2,
            'endurance' => ($goblin->getAttributes()->get('endurance')?->getValue() ?? 0) - 2,
            'agility'   => ($goblin->getAttributes()->get('agility')?->getValue() ?? 0) - 2,
            'intuition' => ($goblin->getAttributes()->get('intuition')?->getValue() ?? 0) - 2,
            'vitality'  => ($goblin->getAttributes()->get('vitality')?->getValue() ?? 0) - 6,
            'attack'    => ($goblin->getAttributes()->get('attack')?->getValue() ?? 0) - 4,
            'defense'   => ($goblin->getAttributes()->get('defense')?->getValue() ?? 0) - 4,
        ];

        $lesserAutoLeveling = [
            'strength'  => ($lesserGoblin->getAttributes()->get('strength')?->getValue() ?? 0),
            'endurance' => ($lesserGoblin->getAttributes()->get('endurance')?->getValue() ?? 0),
            'agility'   => ($lesserGoblin->getAttributes()->get('agility')?->getValue() ?? 0),
            'intuition' => ($lesserGoblin->getAttributes()->get('intuition')?->getValue() ?? 0),
            'vitality'  => ($lesserGoblin->getAttributes()->get('vitality')?->getValue() ?? 0),
            'attack'    => ($lesserGoblin->getAttributes()->get('attack')?->getValue() ?? 0),
            'defense'   => ($lesserGoblin->getAttributes()->get('defense')?->getValue() ?? 0),
        ];


        self::assertSame($betterExpected, $betterAutoLeveling);
        self::assertSame($lesserExpected, $lesserAutoLeveling);
    }
}
