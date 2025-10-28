<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Helper;

use Velkuns\GameTextEngine\Api\AbilitiesApi;
use Velkuns\GameTextEngine\Api\BestiaryApi;
use Velkuns\GameTextEngine\Api\ItemsApi;
use Velkuns\GameTextEngine\Api\PlayerApi;
use Velkuns\GameTextEngine\Api\StatusesApi;
use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Item\ItemInterface;
use Velkuns\GameTextEngine\Element\Modifier\ModifierProcessor;
use Velkuns\GameTextEngine\Element\Resolver\TypeElementResolver;
use Velkuns\GameTextEngine\Utils\Loader\JsonLoader;

/**
 * @phpstan-import-type BestiaryData from BestiaryApi
 * @phpstan-import-type ItemData from ItemInterface
 * @phpstan-import-type EntityData from EntityInterface
 * @phpstan-import-type AbilitiesRulesData from AbilitiesApi
 * @phpstan-import-type StatusesRulesData from StatusesApi
 */
trait ApiTrait
{
    use FactoryTrait;

    private static ?BestiaryApi $bestiary = null;
    private static ?ItemsApi $items = null;
    private static ?AbilitiesApi $abilitiesApi = null;
    private static ?StatusesApi $statusesApi = null;
    private static ?PlayerApi $playerApi = null;

    private static function getBestiaryApi(): BestiaryApi
    {
        if (self::$bestiary === null) {
            self::$bestiary = new BestiaryApi(self::getEntityFactory(), self::getItemsApi());

            /** @var list<BestiaryData> $data */
            $data = (new JsonLoader())->fromFile(__DIR__ . '/../../data/bestiary.json');
            self::$bestiary->load($data);
        }

        return self::$bestiary;
    }


    private static function getItemsApi(): ItemsApi
    {
        if (self::$items === null) {
            self::$items = new ItemsApi(self::getItemFactory());

            /** @var list<ItemData> $data */
            $data = (new JsonLoader())->fromFile(__DIR__ . '/../../data/items.json');
            self::$items->load($data);
        }

        return self::$items;
    }

    private static function getAbilitiesApi(): AbilitiesApi
    {
        if (self::$abilitiesApi === null) {
            self::$abilitiesApi = new AbilitiesApi(self::getAbilityFactory());

            /** @var AbilitiesRulesData $data */
            $data = (new JsonLoader())->fromFile(__DIR__ . '/../../data/rules/rules_abilities.json');
            self::$abilitiesApi->load($data);
        }

        return self::$abilitiesApi;
    }

    private static function getStatusesApi(): StatusesApi
    {
        if (self::$statusesApi === null) {
            self::$statusesApi = new StatusesApi(self::getStatusFactory());

            /** @var StatusesRulesData $data */
            $data = (new JsonLoader())->fromFile(__DIR__ . '/../../data/rules/rules_statuses.json');
            self::$statusesApi->load($data);
        }

        return self::$statusesApi;
    }

    private static function getPlayerApi(): PlayerApi
    {
        if (self::$playerApi === null) {
            self::$playerApi = new PlayerApi(
                self::getEntityFactory(),
                self::getItemsApi(),
                self::getAbilitiesApi(),
                self::getStatusesApi(),
                new ModifierProcessor(new TypeElementResolver()),
            );

            /** @var EntityData $data */
            $data = (new JsonLoader())->fromFile(__DIR__ . '/../../data/templates/player.json');
            self::$playerApi->load($data);
        }

        return self::$playerApi;
    }
}
