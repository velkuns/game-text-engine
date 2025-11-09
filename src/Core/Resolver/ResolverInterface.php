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
use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

interface ResolverInterface
{
    public function supports(string $type): bool;

    public function resolve(string $type, EntityInterface $entity): object;

    /**
     * @param list<Modifier> $modifiers
     */
    public function resolveValue(string $type, EntityInterface $entity, array $modifiers = []): int|float|bool;
}
