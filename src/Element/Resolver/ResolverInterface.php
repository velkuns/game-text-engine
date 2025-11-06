<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Resolver;

use Velkuns\GameTextEngine\Element\Entity\EntityInterface;

interface ResolverInterface
{
    public function supports(string $type): bool;

    public function resolve(string $type, EntityInterface $entity): object;
}
