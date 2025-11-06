<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Helper;

use Velkuns\GameTextEngine\Element\Condition\ConditionParser;
use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Factory\AbilityFactory;
use Velkuns\GameTextEngine\Element\Factory\ConditionsFactory;
use Velkuns\GameTextEngine\Element\Factory\DamageFactory;
use Velkuns\GameTextEngine\Element\Factory\ElementFactory;
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
    use ResolverTrait;
    use ValidatorTrait;

    private static ?EntityFactory $entityFactory = null;

    private static function getEntityFactory(): EntityFactory
    {
        if (self::$entityFactory === null) {
            self::$entityFactory = new EntityFactory(
                self::getAbilityFactory(),
                self::getStatusFactory(),
                self::getItemFactory(),
                self::getDamageFactory(),
            );
        }

        return self::$entityFactory;
    }

    private static function getElementFactory(): ElementFactory
    {
        return new ElementFactory(
            self::getEntityFactory(),
            self::getAbilityFactory(),
            self::getStatusFactory(),
            self::getItemFactory(),
            self::getConditionFactory(),
            self::getModifierFactory(),
        );
    }

    private static function getAbilityFactory(): AbilityFactory
    {
        return new AbilityFactory();
    }

    private static function getConditionFactory(): ConditionsFactory
    {
        return new ConditionsFactory(
            new ConditionParser(),
            self::getResolverHandler(),
            self::getValidatorHandler(),
        );
    }

    private static function getItemFactory(): ItemFactory
    {
        return new ItemFactory(self::getModifierFactory(), self::getDamageFactory());
    }

    private static function getModifierFactory(): ModifierFactory
    {
        return new ModifierFactory(self::getConditionFactory());
    }

    private static function getStatusFactory(): StatusFactory
    {
        return new StatusFactory(self::getModifierFactory());
    }

    private static function getDamageFactory(): DamageFactory
    {
        return new DamageFactory();
    }

    private static function getGraphFactory(): GraphFactory
    {
        return new GraphFactory(self::getConditionFactory());
    }
}
