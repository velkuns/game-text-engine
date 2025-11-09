<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Resolver;

use Velkuns\GameTextEngine\Exception\Core\UnsupportedTypeElementResolverException;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;

readonly class TypeResolverHandler
{
    /**
     * @param list<ResolverInterface> $typeResolvers
     */
    public function __construct(
        private array $typeResolvers = [],
    ) {}

    public function handle(string $type, EntityInterface $entity): object
    {
        $type = \substr($type, (int) \strpos($type, '.') + 1); // Remove self.|enemy. prefix
        foreach ($this->typeResolvers as $resolver) {
            if ($resolver->supports($type)) {
                return $resolver->resolve($type, $entity);
            }
        }

        throw new UnsupportedTypeElementResolverException('Unsupported type: ' . $type);
    }
}
