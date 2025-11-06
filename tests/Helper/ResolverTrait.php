<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Helper;

use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Resolver\AbilityResolver;
use Velkuns\GameTextEngine\Element\Resolver\DamagesResolver;
use Velkuns\GameTextEngine\Element\Resolver\EntityInfoResolver;
use Velkuns\GameTextEngine\Element\Resolver\EntityInventoryItemsResolver;
use Velkuns\GameTextEngine\Element\Resolver\StatusResolver;
use Velkuns\GameTextEngine\Element\Resolver\TypeElementResolverHandler;

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
