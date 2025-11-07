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
use Velkuns\GameTextEngine\Api\BestiaryApi;
use Velkuns\GameTextEngine\Api\Exception\BestiaryException;
use Velkuns\GameTextEngine\Api\ItemsApi;
use Velkuns\GameTextEngine\Element\Item\ItemInterface;
use Velkuns\GameTextEngine\Tests\Helper\ApiTrait;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;
use Velkuns\GameTextEngine\Utils\Loader\JsonLoader;

/**
 * @phpstan-import-type ItemData from ItemInterface
 * @phpstan-import-type BestiaryData from BestiaryApi
 */
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

        self::assertArrayHasKey('strength', $entityData['abilities']['bases']);

        $entityData['abilities']['bases']['strength']['value'] = 20;

        $goblinWarrior = self::getEntityFactory()->from($entityData);

        $bestiary->set($goblinWarrior);

        $goblinWarriorFromBestiary = $bestiary->get('Goblin Warrior', false);

        self::assertSame($goblinWarrior, $goblinWarriorFromBestiary); // get as no clone
        self::assertSame(20, $goblinWarriorFromBestiary->getAbilities()->get('strength')?->getValue());

        //~ Now remove original Goblin entity and check it is removed
        $bestiary->remove($entity->getName());
        self::expectException(BestiaryException::class);
        self::expectExceptionCode(1701);
        $bestiary->get('Goblin');
    }

    public function testLoadWhenThrowException(): void
    {
        $bestiary = self::getBestiaryApi();

        self::expectException(BestiaryException::class);
        self::expectExceptionMessage("Entity 'King Goblin' not found in bestiary.");
        $bestiary->get('King Goblin');
    }
}
