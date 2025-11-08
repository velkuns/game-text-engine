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
use Velkuns\GameTextEngine\Api\AbilitiesApi;
use Velkuns\GameTextEngine\Core\Loader\JsonLoader;
use Velkuns\GameTextEngine\Exception\Api\AbilitiesApiException;
use Velkuns\GameTextEngine\Rpg\Ability\BaseAbility;
use Velkuns\GameTextEngine\Rpg\Ability\CompoundAbility;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;

/**
 * @phpstan-import-type AbilitiesRulesData from AbilitiesApi
 */
class AbilitiesApiTest extends TestCase
{
    use FactoryTrait;

    public function testLoad(): void
    {
        $api = $this->getApi();

        self::assertSame("This section defines base and compound abilities for characters.

The system is based on four primary base abilities: strength, endurance, agility, and intuition. Each base ability has an initial value of 10 and a maximum value of 20.

Compound abilities are derived from base abilities. For example, the 'attack' compound ability is calculated as the sum of strength and agility, while the 'defense' compound ability is calculated as the sum of endurance and intuition.

Abilities values are constrained within defined limits to ensure balanced gameplay.

Medium values are 10, and are between 0 and 20.", $api->rules->description);
        self::assertSame(10, $api->rules->starting->attributionPoints);
        self::assertSame(5, $api->rules->starting->attributionPointsMaxPerAbility);
        self::assertSame(5, $api->rules->leveling->attributionPoints);
        self::assertSame(2, $api->rules->leveling->attributionPointsMaxPerAbility);
    }

    public function testDump(): void
    {
        $api = $this->getApi();
        $content = (string) \file_get_contents($this->getDataDir() . '/rules/rules_abilities.json');

        self::assertSame(\trim($content), $api->dump(true));
    }

    public function testGetAll(): void
    {
        $api = $this->getApi();

        $abilities = $api->getAll();
        self::assertCount(5, $abilities['bases']);
        self::assertCount(2, $abilities['compounds']);
    }

    public function testGetAndSetAndRemove(): void
    {
        $api = $this->getApi();

        $baseAbility     = new BaseAbility('test', value: 12, initial: 12);
        $api->set($baseAbility);
        $compoundAbility = new CompoundAbility('test_compound', 'test + strength', $api->rules->baseAbilities);
        $api->set($compoundAbility);

        self::assertSame($baseAbility, $api->get('test', false));
        self::assertSame($compoundAbility, $api->get('test_compound', false));
        self::assertNotSame($baseAbility, $api->get('test'));
        self::assertNotSame($compoundAbility, $api->get('test_compound'));
        self::assertEquals($baseAbility, $api->get('test'));
        self::assertEquals($compoundAbility, $api->get('test_compound'));

        $api->remove('test_compound');
        self::assertNull($api->get('test_compound'));
        $api->remove('test');
        self::assertNull($api->get('test'));

        self::expectException(AbilitiesApiException::class);
        self::expectExceptionCode(1450);
        $api->remove('test');
    }

    public function testFromNewPlayerButHaveUnknowAbility(): void
    {
        $api = $this->getApi();

        $abilities = [
            'strength'  => 10,
            'endurance' => 10,
            'agility'   => 10,
            'intuition' => 10,
            'unknown'   => 10,
        ];

        self::expectException(AbilitiesApiException::class);
        self::expectExceptionCode(1451);

        $api->fromNewPlayer($abilities);
    }

    public function testFromNewPlayerButAbilityValueAttributedIsMoreThanDefinedInRules(): void
    {
        $api = $this->getApi();

        $abilities = [
            'strength'  => 10,
            'endurance' => 10,
            'agility'   => 10,
            'intuition' => 16,
        ];

        self::expectException(AbilitiesApiException::class);
        self::expectExceptionCode(1452);

        $api->fromNewPlayer($abilities);
    }

    public function testFromNewPlayerButAbilityValueIsLowerThanBaseDefined(): void
    {
        $api = $this->getApi();

        $abilities = [
            'strength'  => 10,
            'endurance' => 10,
            'agility'   => 10,
            'intuition' => 8,
        ];

        self::expectException(AbilitiesApiException::class);
        self::expectExceptionCode(1453);

        $api->fromNewPlayer($abilities);
    }

    public function testFromNewPlayerButTotalAttributedPointsIsMoreThanDefinedInRules(): void
    {
        $api = $this->getApi();

        $abilities = [
            'strength'  => 15,
            'endurance' => 15,
            'agility'   => 15,
            'intuition' => 15,
        ];

        self::expectException(AbilitiesApiException::class);
        self::expectExceptionCode(1454);

        $api->fromNewPlayer($abilities);
    }

    public function testFromNewPlayerButItIsRemainingSomePointsToAttribute(): void
    {
        $api = $this->getApi();

        $abilities = [
            'strength'  => 12,
            'endurance' => 12,
            'agility'   => 12,
            'intuition' => 12,
        ];

        self::expectException(AbilitiesApiException::class);
        self::expectExceptionCode(1455);

        $api->fromNewPlayer($abilities);
    }

    private function getApi(): AbilitiesApi
    {
        $loader  = new JsonLoader();
        $api     = new AbilitiesApi(self::getAbilityFactory());

        /** @var AbilitiesRulesData $data */
        $data = $loader->fromFile($this->getDataDir() . '/rules/rules_abilities.json');
        $api->load($data);

        return $api;
    }

    private function getDataDir(): string
    {
        return (string) realpath(__DIR__ . '/../../../data');
    }
}
