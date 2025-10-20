<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Helper;

use Velkuns\GameTextEngine\Element\Condition\ConditionElementResolver;
use Velkuns\GameTextEngine\Element\Condition\ConditionParser;
use Velkuns\GameTextEngine\Element\Condition\ConditionValidator;
use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Factory\AbilityFactory;
use Velkuns\GameTextEngine\Element\Factory\ConditionsFactory;
use Velkuns\GameTextEngine\Element\Factory\EntityFactory;
use Velkuns\GameTextEngine\Element\Factory\ItemFactory;
use Velkuns\GameTextEngine\Element\Factory\ModifierFactory;
use Velkuns\GameTextEngine\Element\Factory\StatusFactory;
use Velkuns\GameTextEngine\Graph\Factory\GraphFactory;

/**
 * @phpstan-import-type EntityData from EntityInterface
 */
trait FactoryTrait
{
    private static ?EntityFactory $entityFactory = null;

    private static function getEntityFactory(): EntityFactory
    {
        if (self::$entityFactory === null) {
            self::$entityFactory = new EntityFactory(
                self::getAbilityFactory(),
                self::getStatusFactory(),
                self::getItemFactory(),
            );
        }

        return self::$entityFactory;
    }

    private static function getAbilityFactory(): AbilityFactory
    {
        return new AbilityFactory();
    }

    private static function getConditionFactory(): ConditionsFactory
    {
        return new ConditionsFactory(
            new ConditionParser(),
            new ConditionElementResolver(),
            new ConditionValidator(),
        );
    }

    private static function getItemFactory(): ItemFactory
    {
        return new ItemFactory(self::getModifierFactory());
    }

    private static function getModifierFactory(): ModifierFactory
    {
        return new ModifierFactory();
    }

    private static function getStatusFactory(): StatusFactory
    {
        return new StatusFactory(self::getModifierFactory(), self::getConditionFactory());
    }

    private static function getGraphFactory(): GraphFactory
    {
        return new GraphFactory(self::getConditionFactory());
    }
}
