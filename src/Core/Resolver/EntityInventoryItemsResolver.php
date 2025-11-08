<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Resolver;

use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInventory;

readonly class EntityInventoryItemsResolver implements ResolverInterface
{
    public function supports(string $type): bool
    {
        return $type === 'inventory.item';
    }

    public function resolve(string $type, EntityInterface $entity): EntityInventory
    {
        return $entity->getInventory();
    }
}
