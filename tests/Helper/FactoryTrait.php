<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Helper;

use Velkuns\GameTextEngine\Core\Factory\AlterationFactory;
use Velkuns\GameTextEngine\Core\Prerequisite\ConditionParser;
use Velkuns\GameTextEngine\Core\Factory\AttributeFactory;
use Velkuns\GameTextEngine\Core\Factory\PrerequisitesFactory;
use Velkuns\GameTextEngine\Core\Factory\DamageFactory;
use Velkuns\GameTextEngine\Core\Factory\ElementFactory;
use Velkuns\GameTextEngine\Core\Factory\EntityFactory;
use Velkuns\GameTextEngine\Core\Factory\ItemFactory;
use Velkuns\GameTextEngine\Core\Factory\LootFactory;
use Velkuns\GameTextEngine\Core\Factory\ModifierFactory;
use Velkuns\GameTextEngine\Core\Factory\TraitFactory;
use Velkuns\GameTextEngine\Graph\Factory\GraphFactory;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;

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
                self::getAttributeFactory(),
                self::getTraitFactory(),
                self::getAlterationFactory(),
                self::getItemFactory(),
                self::getDamageFactory(),
                self::getLootFactory(),
            );
        }

        return self::$entityFactory;
    }

    private static function getElementFactory(): ElementFactory
    {
        return new ElementFactory(
            self::getEntityFactory(),
            self::getAttributeFactory(),
            self::getTraitFactory(),
            self::getAlterationFactory(),
            self::getItemFactory(),
            self::getPrerequisitesFactory(),
            self::getModifierFactory(),
        );
    }

    private static function getAttributeFactory(): AttributeFactory
    {
        return new AttributeFactory();
    }

    private static function getLootFactory(): LootFactory
    {
        return new LootFactory();
    }

    private static function getPrerequisitesFactory(): PrerequisitesFactory
    {
        return new PrerequisitesFactory(
            new ConditionParser(),
            self::getTypeResolverHandler(),
            self::getValidatorHandler(),
        );
    }

    private static function getItemFactory(): ItemFactory
    {
        return new ItemFactory(self::getModifierFactory(), self::getDamageFactory());
    }

    private static function getModifierFactory(): ModifierFactory
    {
        return new ModifierFactory(self::getPrerequisitesFactory());
    }

    private static function getTraitFactory(): TraitFactory
    {
        return new TraitFactory(self::getModifierFactory());
    }

    private static function getAlterationFactory(): AlterationFactory
    {
        return new AlterationFactory(self::getModifierFactory());
    }

    private static function getDamageFactory(): DamageFactory
    {
        return new DamageFactory();
    }

    private static function getGraphFactory(): GraphFactory
    {
        return new GraphFactory(self::getPrerequisitesFactory());
    }
}
