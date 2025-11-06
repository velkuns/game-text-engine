<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Resolver;

use Velkuns\GameTextEngine\Element\Entity\EntityInfo;
use Velkuns\GameTextEngine\Element\Entity\EntityInterface;

readonly class EntityInfoResolver implements ResolverInterface
{
    public function supports(string $type): bool
    {
        return $type === 'info';
    }

    public function resolve(string $type, EntityInterface $entity): EntityInfo
    {
        return $entity->getInfo();
    }
}
