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
use Velkuns\GameTextEngine\Api\AttributesApi;
use Velkuns\GameTextEngine\Core\Loader\JsonLoader;
use Velkuns\GameTextEngine\Exception\Api\AttributesApiException;
use Velkuns\GameTextEngine\Rpg\Attribute\SimpleAttribute;
use Velkuns\GameTextEngine\Rpg\Attribute\CompoundAttribute;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;

/**
 * @phpstan-import-type AttributesRulesData from AttributesApi
 */
class AttributesApiTest extends TestCase
{
    use FactoryTrait;

    public function testLoad(): void
    {
        $api = $this->getApi();

        self::assertSame("This section defines simple and compound attributes for characters.

The system is based on four primary simple attributes: strength, endurance, agility, and intuition. Each simple attribute has an initial value of 10 and a maximum value of 20.

Compound attributes are derived from simple attributes. For example, the 'attack' compound attribute is calculated as the sum of strength and agility, while the 'defense' compound attribute is calculated as the sum of endurance and intuition.

Attributes values are constrained within defined limits to ensure balanced gameplay.

Medium values are 10, and are between 0 and 20.", $api->rules->description);
        self::assertSame(10, $api->rules->starting->attributionPoints);
        self::assertSame(5, $api->rules->starting->attributionPointsMaxPerAttribute);
        self::assertSame(5, $api->rules->leveling->attributionPoints);
        self::assertSame(2, $api->rules->leveling->attributionPointsMaxPerAttribute);
    }

    public function testDump(): void
    {
        $api = $this->getApi();
        $content = (string) \file_get_contents($this->getDataDir() . '/rules/rules_attributes.json');

        self::assertSame(\trim($content), $api->dump(true));
    }

    public function testGetAll(): void
    {
        $api = $this->getApi();

        $attributes = $api->getAll();
        self::assertCount(5, $attributes['simples']);
        self::assertCount(2, $attributes['compounds']);
    }

    public function testGetAndSetAndRemove(): void
    {
        $api = $this->getApi();

        $simpleAttribute     = new SimpleAttribute('test', value: 12, initial: 12);
        $api->set($simpleAttribute);
        $compoundAttribute = new CompoundAttribute('test_compound', 'test + strength', $api->rules->simpleAttributes);
        $api->set($compoundAttribute);

        self::assertSame($simpleAttribute, $api->get('test', false));
        self::assertSame($compoundAttribute, $api->get('test_compound', false));
        self::assertNotSame($simpleAttribute, $api->get('test'));
        self::assertNotSame($compoundAttribute, $api->get('test_compound'));
        self::assertEquals($simpleAttribute, $api->get('test'));
        self::assertEquals($compoundAttribute, $api->get('test_compound'));

        $api->remove('test_compound');
        self::assertNull($api->get('test_compound'));
        $api->remove('test');
        self::assertNull($api->get('test'));

        self::expectException(AttributesApiException::class);
        self::expectExceptionCode(1450);
        $api->remove('test');
    }

    public function testFromNewPlayerButHaveUnknowAttribute(): void
    {
        $api = $this->getApi();

        $attributes = [
            'strength'  => 10,
            'endurance' => 10,
            'agility'   => 10,
            'intuition' => 10,
            'unknown'   => 10,
        ];

        self::expectException(AttributesApiException::class);
        self::expectExceptionCode(1451);

        $api->fromNewPlayer($attributes);
    }

    public function testFromNewPlayerButAttributeValueAttributedIsMoreThanDefinedInRules(): void
    {
        $api = $this->getApi();

        $attributes = [
            'strength'  => 10,
            'endurance' => 10,
            'agility'   => 10,
            'intuition' => 16,
        ];

        self::expectException(AttributesApiException::class);
        self::expectExceptionCode(1452);

        $api->fromNewPlayer($attributes);
    }

    public function testFromNewPlayerButAttributeValueIsLowerThanSimpleDefined(): void
    {
        $api = $this->getApi();

        $attributes = [
            'strength'  => 10,
            'endurance' => 10,
            'agility'   => 10,
            'intuition' => 8,
        ];

        self::expectException(AttributesApiException::class);
        self::expectExceptionCode(1453);

        $api->fromNewPlayer($attributes);
    }

    public function testFromNewPlayerButTotalAttributedPointsIsMoreThanDefinedInRules(): void
    {
        $api = $this->getApi();

        $attributes = [
            'strength'  => 15,
            'endurance' => 15,
            'agility'   => 15,
            'intuition' => 15,
        ];

        self::expectException(AttributesApiException::class);
        self::expectExceptionCode(1454);

        $api->fromNewPlayer($attributes);
    }

    public function testFromNewPlayerButItIsRemainingSomePointsToAttribute(): void
    {
        $api = $this->getApi();

        $attributes = [
            'strength'  => 12,
            'endurance' => 12,
            'agility'   => 12,
            'intuition' => 12,
        ];

        self::expectException(AttributesApiException::class);
        self::expectExceptionCode(1455);

        $api->fromNewPlayer($attributes);
    }

    private function getApi(): AttributesApi
    {
        $loader  = new JsonLoader();
        $api     = new AttributesApi(self::getAttributeFactory());

        /** @var AttributesRulesData $data */
        $data = $loader->fromFile($this->getDataDir() . '/rules/rules_attributes.json');
        $api->load($data);

        return $api;
    }

    private function getDataDir(): string
    {
        return (string) realpath(__DIR__ . '/../../../data');
    }
}
