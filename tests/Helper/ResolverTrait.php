<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Helper;

use Velkuns\GameTextEngine\Core\Resolver\AbilityResolver;
use Velkuns\GameTextEngine\Core\Resolver\DamagesResolver;
use Velkuns\GameTextEngine\Core\Resolver\EntityInfoResolver;
use Velkuns\GameTextEngine\Core\Resolver\EntityInventoryItemsResolver;
use Velkuns\GameTextEngine\Core\Resolver\StatusResolver;
use Velkuns\GameTextEngine\Core\Resolver\TypeElementResolverHandler;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;

/**
 * @phpstan-import-type EntityData from EntityInterface
 */
trait ResolverTrait
{
    private static function getResolverHandler(): TypeElementResolverHandler
    {
        $resolvers = [
            new AbilityResolver(),
            new DamagesResolver(),
            new EntityInfoResolver(),
            new EntityInventoryItemsResolver(),
            new StatusResolver(),
        ];

        return new TypeElementResolverHandler($resolvers);
    }
}
