<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Helper;

use Random\Engine\Mt19937;
use Random\Randomizer;
use Velkuns\GameTextEngine\Api\AlterationsApi;
use Velkuns\GameTextEngine\Api\AttributesApi;
use Velkuns\GameTextEngine\Api\BestiaryApi;
use Velkuns\GameTextEngine\Api\CombatApi;
use Velkuns\GameTextEngine\Api\ItemsApi;
use Velkuns\GameTextEngine\Api\PlayerApi;
use Velkuns\GameTextEngine\Api\TraitsApi;
use Velkuns\GameTextEngine\Core\Evaluator\Evaluator;
use Velkuns\GameTextEngine\Core\Loader\JsonLoader;
use Velkuns\GameTextEngine\Core\Processor\TimeProcessor;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;
use Velkuns\GameTextEngine\Rpg\Item\ItemInterface;
use Velkuns\GameTextEngine\Rpg\Modifier\AttributeModifierProcessor;
use Velkuns\GameTextEngine\Rpg\Modifier\DamagesModifierProcessor;
use Velkuns\GameTextEngine\Rpg\Modifier\ModifierHandler;
use Velkuns\GameTextEngine\Rules\Alterations\AlterationsRules;
use Velkuns\GameTextEngine\Rules\Attributes\AttributesRules;
use Velkuns\GameTextEngine\Rules\Combat\CombatRules;
use Velkuns\GameTextEngine\Rules\Player\PlayerRules;
use Velkuns\GameTextEngine\Rules\Traits\TraitsRules;

/**
 * @phpstan-import-type BestiaryFileData from BestiaryApi
 * @phpstan-import-type ItemData from ItemInterface
 * @phpstan-import-type EntityData from EntityInterface
 * @phpstan-import-type AttributesRulesData from AttributesRules
 * @phpstan-import-type TraitsRulesData from TraitsRules
 * @phpstan-import-type AlterationsRulesData from AlterationsRules
 * @phpstan-import-type CombatRulesData from CombatRules
 * @phpstan-import-type PlayerRulesData from PlayerRules
 */
trait ApiTrait
{
    use FactoryTrait;
    use ResolverTrait;

    private static ?BestiaryApi $bestiary = null;
    private static ?ItemsApi $items = null;
    private static ?AttributesApi $attributesApi = null;
    private static ?TraitsApi $traitsApi = null;
    private static ?AlterationsApi $alterationsApi = null;
    private static ?PlayerApi $playerApi = null;
    private static ?CombatApi $combatApi = null;

    private static function getBestiaryApi(int $seed = 42): BestiaryApi
    {
        if (self::$bestiary === null) {
            self::$bestiary = new BestiaryApi(
                new Randomizer(new Mt19937($seed)),
                self::getEntityFactory(),
                self::getItemsApi(),
            );

            /** @var BestiaryFileData $data */
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

    private static function getCombatApi(int $seed = 42): CombatApi
    {
        if (self::$combatApi === null) {
            self::$combatApi = new CombatApi(
                new Evaluator(self::getValueResolverHandler($seed)),
                new Randomizer(new Mt19937($seed)),
                new TimeProcessor(),
                self::getItemsApi(),
            );

            /** @var CombatRulesData $data */
            $data = (new JsonLoader())->fromFile(__DIR__ . '/../../data/rules/rules_combat.json');
            self::$combatApi->load($data);
        }

        return self::$combatApi;
    }

    private static function getAttributesApi(): AttributesApi
    {
        if (self::$attributesApi === null) {
            self::$attributesApi = new AttributesApi(self::getAttributeFactory());

            /** @var AttributesRulesData $data */
            $data = (new JsonLoader())->fromFile(__DIR__ . '/../../data/rules/rules_attributes.json');
            self::$attributesApi->load($data);
        }

        return self::$attributesApi;
    }

    private static function getTraitsApi(): TraitsApi
    {
        if (self::$traitsApi === null) {
            self::$traitsApi = new TraitsApi(self::getTraitFactory());

            /** @var TraitsRulesData $data */
            $data = (new JsonLoader())->fromFile(__DIR__ . '/../../data/rules/rules_traits.json');
            self::$traitsApi->load($data);
        }

        return self::$traitsApi;
    }

    private static function getAlterationsApi(): AlterationsApi
    {
        if (self::$alterationsApi === null) {
            self::$alterationsApi = new AlterationsApi(self::getAlterationFactory());

            /** @var AlterationsRulesData $data */
            $data = (new JsonLoader())->fromFile(__DIR__ . '/../../data/rules/rules_alterations.json');
            self::$alterationsApi->load($data);
        }

        return self::$alterationsApi;
    }

    private static function getPlayerApi(): PlayerApi
    {
        if (self::$playerApi === null) {
            self::$playerApi = new PlayerApi(
                self::getEntityFactory(),
                self::getItemsApi(),
                self::getAttributesApi(),
                self::getTraitsApi(),
                self::getAlterationsApi(),
                new ModifierHandler(self::getTypeResolverHandler(), [new AttributeModifierProcessor(), new DamagesModifierProcessor()]),
                new Evaluator(self::getValueResolverHandler()),
            );

            /** @var PlayerRulesData $playerRulesData */
            $playerRulesData = (new JsonLoader())->fromFile(__DIR__ . '/../../data/rules/rules_player.json');

            /** @var EntityData $playerData */
            $playerData = (new JsonLoader())->fromFile(__DIR__ . '/../../data/templates/player.json');
            self::$playerApi->load($playerRulesData, $playerData);
        }

        return self::$playerApi;
    }
}
