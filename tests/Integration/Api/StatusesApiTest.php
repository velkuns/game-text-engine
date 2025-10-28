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
use Velkuns\GameTextEngine\Api\Exception\StatusesApiException;
use Velkuns\GameTextEngine\Api\StatusesApi;
use Velkuns\GameTextEngine\Element\Status\Status;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;
use Velkuns\GameTextEngine\Utils\Loader\JsonLoader;

/**
 * @phpstan-import-type StatusesRulesData from StatusesApi
 */
class StatusesApiTest extends TestCase
{
    use FactoryTrait;

    public function testLoad(): void
    {
        $api = $this->getApi();
        $expectedAttributions = ['skill' => 2, 'state' => 0, 'blessing' => 0, 'curse' => 0, 'title' => 0];

        self::assertSame("This section defines all kind of statuses types available for the story, and the number of each when create a new character.", $api->description);
        self::assertSame($expectedAttributions, $api->attributions);
    }

    public function testDump(): void
    {
        $api = $this->getApi();
        $content = (string) \file_get_contents($this->getDataDir() . '/rules/rules_statuses.json');

        self::assertSame(\trim($content), $api->dump(true));
    }

    public function testGetAll(): void
    {
        $api = $this->getApi();

        $statuses = $api->getAll();
        self::assertCount(8, $statuses['skill']);
        self::assertCount(4, $statuses['state']);
        self::assertCount(0, $statuses['blessing']);
        self::assertCount(0, $statuses['curse']);
        self::assertCount(3, $statuses['title']);
    }

    public function testGetAndSetAndRemove(): void
    {
        $api = $this->getApi();

        self::assertNull($api->get('skill', 'New Skill'));

        $newSkill = new Status('skill', 'New Skill', 'description of new skill', []);
        $api->set($newSkill);

        self::assertSame($newSkill, $api->get('skill', 'New Skill', false));
        self::assertNotSame($newSkill, $api->get('skill', 'New Skill'));
        self::assertEquals($newSkill, $api->get('skill', 'New Skill'));

        $api->remove('skill', 'New Skill');
        self::assertNull($api->get('skill', 'New Skill'));
    }

    public function testSetStatusButTypeDoesNotExist(): void
    {
        $api = $this->getApi();

        $newSkill = new Status('unknown', 'New Skill', 'description of new skill', []);

        self::expectException(StatusesApiException::class);
        self::expectExceptionCode(1550);
        $api->set($newSkill);
    }

    public function testRemoveStatusButItDoesNotExist(): void
    {
        $api = $this->getApi();

        self::expectException(StatusesApiException::class);
        self::expectExceptionCode(1551);
        $api->remove('skill', 'New Skill');
    }

    public function testFromNewPlayer(): void
    {
        $api = $this->getApi();

        $statuses = ['skill' => ['Goblin Hunter', 'Wolf Hunter']];

        $data = $api->fromNewPlayer($statuses);

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

        $statuses = ['unknown' => ['Goblin Hunter', 'Wolf Hunter']];

        self::expectException(StatusesApiException::class);
        self::expectExceptionCode(1552);

        $api->fromNewPlayer($statuses);
    }

    public function testFromNewPlayerButStatusDoesNotExistForGivenType(): void
    {
        $api = $this->getApi();

        $statuses = ['skill' => ['Dragon Hunter']];

        self::expectException(StatusesApiException::class);
        self::expectExceptionCode(1553);

        $api->fromNewPlayer($statuses);
    }

    public function testFromNewPlayerButThereIsTooManyStatusForGivenType(): void
    {
        $api = $this->getApi();

        $statuses = ['skill' => ['Goblin Hunter', 'Wolf Hunter', 'Lockpicking']];

        self::expectException(StatusesApiException::class);
        self::expectExceptionCode(1554);

        $api->fromNewPlayer($statuses);
    }

    public function testFromNewPlayerButThereIsTooFewStatusForGivenType(): void
    {
        $api = $this->getApi();

        $statuses = ['skill' => ['Goblin Hunter']];

        self::expectException(StatusesApiException::class);
        self::expectExceptionCode(1555);

        $api->fromNewPlayer($statuses);
    }

    private function getApi(): StatusesApi
    {
        $loader  = new JsonLoader();
        $api     = new StatusesApi(self::getStatusFactory());

        /** @var StatusesRulesData $data */
        $data = $loader->fromFile($this->getDataDir() . '/rules/rules_statuses.json');
        $api->load($data);

        return $api;
    }

    private function getDataDir(): string
    {
        return (string) realpath(__DIR__ . '/../../../data');
    }
}
