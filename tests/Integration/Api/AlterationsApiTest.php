<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Api;

use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Api\AlterationsApi;
use Velkuns\GameTextEngine\Core\Loader\JsonLoader;
use Velkuns\GameTextEngine\Exception\Api\AlterationsApiException;
use Velkuns\GameTextEngine\Rpg\Alteration\Alteration;
use Velkuns\GameTextEngine\Rpg\Alteration\AlterationDuration;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;

/**
 * @phpstan-import-type AlterationsRulesData from AlterationsApi
 */
class AlterationsApiTest extends TestCase
{
    use FactoryTrait;

    public function testLoad(): void
    {
        $api = $this->getApi();
        $expectedStartingAttributions = ['state' => ['number' => 1], 'blessing' => [], 'curse' => []];
        $expectedLevelingAttributions = ['state' => ['number' => 1, 'everyNumberLevel' => 2], 'blessing' => [], 'curse' => []];

        self::assertSame("This section defines all kind of alterations types available for the story, and the number of each when create a new character or leveling character.\nAlteration have duration.", $api->rules->description);
        self::assertSame($expectedStartingAttributions, $api->rules->starting->attributions);
        self::assertSame($expectedLevelingAttributions, $api->rules->leveling->attributions);
        self::assertSame(2, $api->rules->leveling->getEveryNumberLevel('state'));
    }

    public function testDump(): void
    {
        $api = $this->getApi();
        $content = (string) \file_get_contents($this->getDataDir() . '/rules/rules_alterations.json');

        self::assertSame(\trim($content), $api->dump(true));
    }

    public function testGetAll(): void
    {
        $api = $this->getApi();

        $alterations = $api->getAll();
        self::assertCount(4, $alterations['state']);
        self::assertCount(2, $alterations['blessing']);
        self::assertCount(1, $alterations['curse']);
    }

    public function testGetAndSetAndRemove(): void
    {
        $api = $this->getApi();

        self::assertNull($api->get('state', 'New State'));

        $newState = new Alteration('state', 'New State', 'description of new state', [], new AlterationDuration(3, 3));
        $api->set($newState);

        self::assertSame($newState, $api->get('state', 'New State', false));
        self::assertNotSame($newState, $api->get('state', 'New State'));
        self::assertEquals($newState, $api->get('state', 'New State'));

        $api->remove('state', 'New State');
        self::assertNull($api->get('state', 'New State'));
    }

    public function testSetAlterationButTypeDoesNotExist(): void
    {
        $api = $this->getApi();

        $newState = new Alteration('unknown', 'New State', 'description of new state', [], new AlterationDuration(3, 3));

        self::expectException(AlterationsApiException::class);
        self::expectExceptionCode(1550);
        $api->set($newState);
    }

    public function testRemoveAlterationButItDoesNotExist(): void
    {
        $api = $this->getApi();

        self::expectException(AlterationsApiException::class);
        self::expectExceptionCode(1551);
        $api->remove('state', 'New State');
    }

    public function testFromNewPlayer(): void
    {
        $api = $this->getApi();

        $alterations = ['state' => ['poisoned']];

        $data = $api->fromNewPlayer($alterations);

        self::assertCount(1, $data['state']);
        self::assertCount(0, $data['blessing']);
        self::assertCount(0, $data['curse']);
        self::assertSame('poisoned', $data['state']['poisoned']['name']);
    }

    public function testFromNewPlayerButTypeDoesNotExistInRules(): void
    {
        $api = $this->getApi();

        $alterations = ['unknown' => ['poisoned']];

        self::expectException(AlterationsApiException::class);
        self::expectExceptionCode(1552);

        $api->fromNewPlayer($alterations);
    }

    public function testFromNewPlayerButAlterationDoesNotExistForGivenType(): void
    {
        $api = $this->getApi();

        $alterations = ['curse' => ['poisoned']];

        self::expectException(AlterationsApiException::class);
        self::expectExceptionCode(1553);

        $api->fromNewPlayer($alterations);
    }

    public function testFromNewPlayerButThereIsTooManyAlterationForGivenType(): void
    {
        $api = $this->getApi();

        $alterations = ['state' => ['poisoned', 'stunned']];

        self::expectException(AlterationsApiException::class);
        self::expectExceptionCode(1554);

        $api->fromNewPlayer($alterations);
    }

    public function testFromNewPlayerButThereIsTooFewAlterationForGivenType(): void
    {
        $api = $this->getApi();

        $alterations = ['state' => []];

        self::expectException(AlterationsApiException::class);
        self::expectExceptionCode(1555);

        $api->fromNewPlayer($alterations);
    }

    private function getApi(): AlterationsApi
    {
        $loader  = new JsonLoader();
        $api     = new AlterationsApi(self::getAlterationFactory());

        /** @var AlterationsRulesData $data */
        $data = $loader->fromFile($this->getDataDir() . '/rules/rules_alterations.json');
        $api->load($data);

        return $api;
    }

    private function getDataDir(): string
    {
        return (string) realpath(__DIR__ . '/../../../data');
    }
}
