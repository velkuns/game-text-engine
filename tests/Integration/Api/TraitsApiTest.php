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
use Velkuns\GameTextEngine\Api\TraitsApi;
use Velkuns\GameTextEngine\Core\Loader\JsonLoader;
use Velkuns\GameTextEngine\Exception\Api\TraitsApiException;
use Velkuns\GameTextEngine\Rpg\Traits\EntityTrait;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;

/**
 * @phpstan-import-type TraitsRulesData from TraitsApi
 */
class TraitsApiTest extends TestCase
{
    use FactoryTrait;

    public function testLoad(): void
    {
        $api = $this->getApi();
        $expectedStartingAttributions = ['skill' => 2, 'state' => 0, 'blessing' => 0, 'curse' => 0, 'title' => 0];
        $expectedLevelingAttributions = ['skill' => 1, 'state' => 0, 'blessing' => 0, 'curse' => 0, 'title' => 0];

        self::assertSame("This section defines all kind of traits types available for the story, and the number of each when create a new character.", $api->rules->description);
        self::assertSame($expectedStartingAttributions, $api->rules->starting->attributions);
        self::assertSame($expectedLevelingAttributions, $api->rules->leveling->attributions);
        self::assertSame(2, $api->rules->leveling->everyNumberLevel);
    }

    public function testDump(): void
    {
        $api = $this->getApi();
        $content = (string) \file_get_contents($this->getDataDir() . '/rules/rules_traits.json');

        self::assertSame(\trim($content), $api->dump(true));
    }

    public function testGetAll(): void
    {
        $api = $this->getApi();

        $traits = $api->getAll();
        self::assertCount(8, $traits['skill']);
        self::assertCount(4, $traits['state']);
        self::assertCount(0, $traits['blessing']);
        self::assertCount(0, $traits['curse']);
        self::assertCount(3, $traits['title']);
    }

    public function testGetAndSetAndRemove(): void
    {
        $api = $this->getApi();

        self::assertNull($api->get('skill', 'New Skill'));

        $newSkill = new EntityTrait('skill', 'New Skill', 'description of new skill', []);
        $api->set($newSkill);

        self::assertSame($newSkill, $api->get('skill', 'New Skill', false));
        self::assertNotSame($newSkill, $api->get('skill', 'New Skill'));
        self::assertEquals($newSkill, $api->get('skill', 'New Skill'));

        $api->remove('skill', 'New Skill');
        self::assertNull($api->get('skill', 'New Skill'));
    }

    public function testSetTraitButTypeDoesNotExist(): void
    {
        $api = $this->getApi();

        $newSkill = new EntityTrait('unknown', 'New Skill', 'description of new skill', []);

        self::expectException(TraitsApiException::class);
        self::expectExceptionCode(1550);
        $api->set($newSkill);
    }

    public function testRemoveTraitButItDoesNotExist(): void
    {
        $api = $this->getApi();

        self::expectException(TraitsApiException::class);
        self::expectExceptionCode(1551);
        $api->remove('skill', 'New Skill');
    }

    public function testFromNewPlayer(): void
    {
        $api = $this->getApi();

        $traits = ['skill' => ['Goblin Hunter', 'Wolf Hunter']];

        $data = $api->fromNewPlayer($traits);

        self::assertCount(2, $data['skill']);
        self::assertCount(0, $data['state']);
        self::assertCount(0, $data['blessing']);
        self::assertCount(0, $data['curse']);
        self::assertCount(0, $data['title']);
        self::assertSame('Goblin Hunter', $data['skill']['Goblin Hunter']['name']);
        self::assertSame('Wolf Hunter', $data['skill']['Wolf Hunter']['name']);
    }

    public function testFromNewPlayerButTypeDoesNotExistInRules(): void
    {
        $api = $this->getApi();

        $traits = ['unknown' => ['Goblin Hunter', 'Wolf Hunter']];

        self::expectException(TraitsApiException::class);
        self::expectExceptionCode(1552);

        $api->fromNewPlayer($traits);
    }

    public function testFromNewPlayerButTraitDoesNotExistForGivenType(): void
    {
        $api = $this->getApi();

        $traits = ['skill' => ['Dragon Hunter']];

        self::expectException(TraitsApiException::class);
        self::expectExceptionCode(1553);

        $api->fromNewPlayer($traits);
    }

    public function testFromNewPlayerButThereIsTooManyTraitForGivenType(): void
    {
        $api = $this->getApi();

        $traits = ['skill' => ['Goblin Hunter', 'Wolf Hunter', 'Lockpicking']];

        self::expectException(TraitsApiException::class);
        self::expectExceptionCode(1554);

        $api->fromNewPlayer($traits);
    }

    public function testFromNewPlayerButThereIsTooFewTraitForGivenType(): void
    {
        $api = $this->getApi();

        $traits = ['skill' => ['Goblin Hunter']];

        self::expectException(TraitsApiException::class);
        self::expectExceptionCode(1555);

        $api->fromNewPlayer($traits);
    }

    private function getApi(): TraitsApi
    {
        $loader  = new JsonLoader();
        $api     = new TraitsApi(self::getTraitFactory());

        /** @var TraitsRulesData $data */
        $data = $loader->fromFile($this->getDataDir() . '/rules/rules_traits.json');
        $api->load($data);

        return $api;
    }

    private function getDataDir(): string
    {
        return (string) realpath(__DIR__ . '/../../../data');
    }
}
