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
use Velkuns\GameTextEngine\Api\AbilitiesApi;
use Velkuns\GameTextEngine\Api\BestiaryApi;
use Velkuns\GameTextEngine\Api\CombatApi;
use Velkuns\GameTextEngine\Api\ItemsApi;
use Velkuns\GameTextEngine\Api\PlayerApi;
use Velkuns\GameTextEngine\Api\StatusesApi;
use Velkuns\GameTextEngine\Core\Evaluator\Evaluator;
use Velkuns\GameTextEngine\Core\Loader\JsonLoader;
use Velkuns\GameTextEngine\Core\Processor\TimeProcessor;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;
use Velkuns\GameTextEngine\Rpg\Item\ItemInterface;
use Velkuns\GameTextEngine\Rpg\Modifier\AbilityModifierProcessor;
use Velkuns\GameTextEngine\Rpg\Modifier\DamagesModifierProcessor;
use Velkuns\GameTextEngine\Rpg\Modifier\ModifierHandler;
use Velkuns\GameTextEngine\Rules\Abilities\AbilitiesRules;
use Velkuns\GameTextEngine\Rules\Combat\CombatRules;
use Velkuns\GameTextEngine\Rules\Player\PlayerRules;
use Velkuns\GameTextEngine\Rules\Statuses\StatusesRules;

/**
 * @phpstan-import-type BestiaryFileData from BestiaryApi
 * @phpstan-import-type ItemData from ItemInterface
 * @phpstan-import-type EntityData from EntityInterface
 * @phpstan-import-type AbilitiesRulesData from AbilitiesRules
 * @phpstan-import-type StatusesRulesData from StatusesRules
 * @phpstan-import-type CombatRulesData from CombatRules
 * @phpstan-import-type PlayerRulesData from PlayerRules
 */
trait ApiTrait
{
    use FactoryTrait;
    use ResolverTrait;

    private static ?BestiaryApi $bestiary = null;
    private static ?ItemsApi $items = null;
    private static ?AbilitiesApi $abilitiesApi = null;
    private static ?StatusesApi $statusesApi = null;
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
                new ModifierHandler(self::getTypeResolverHandler(), [new AbilityModifierProcessor(), new DamagesModifierProcessor()]),
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
