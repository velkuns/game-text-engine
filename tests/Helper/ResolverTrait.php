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
use Velkuns\GameTextEngine\Core\Resolver\AttributeResolver;
use Velkuns\GameTextEngine\Core\Resolver\EntityDamagesResolver;
use Velkuns\GameTextEngine\Core\Resolver\EntityInfoResolver;
use Velkuns\GameTextEngine\Core\Resolver\EntityInventoryItemsResolver;
use Velkuns\GameTextEngine\Core\Resolver\EquippedWeaponItemResolver;
use Velkuns\GameTextEngine\Core\Resolver\RollResolver;
use Velkuns\GameTextEngine\Core\Resolver\TraitResolver;
use Velkuns\GameTextEngine\Core\Resolver\TypeResolverHandler;
use Velkuns\GameTextEngine\Core\Resolver\ValueResolverHandler;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;

/**
 * @phpstan-import-type EntityData from EntityInterface
 */
trait ResolverTrait
{
    private static function getTypeResolverHandler(): TypeResolverHandler
    {
        $resolvers = [
            new AttributeResolver(),
            new EntityDamagesResolver(),
            new EntityInfoResolver(),
            new EntityInventoryItemsResolver(),
            new TraitResolver(),
        ];

        return new TypeResolverHandler($resolvers);
    }

    private static function getValueResolverHandler(int $seed = 42): ValueResolverHandler
    {
        $resolvers = [
            new AttributeResolver(),
            new EntityDamagesResolver(),
            new EntityInfoResolver(),
            new EquippedWeaponItemResolver(),
            new RollResolver(new Randomizer(new Mt19937($seed))),
        ];

        return new ValueResolverHandler($resolvers);
    }
}
