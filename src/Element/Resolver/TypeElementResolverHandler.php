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
use Velkuns\GameTextEngine\Exception\Element\UnsupportedTypeElementResolverException;

readonly class TypeElementResolverHandler
{
    /**
     * @param list<ResolverInterface> $resolvers
     */
    public function __construct(
        private array $resolvers = [],
    ) {}

    public function handle(string $type, EntityInterface $entity): object
    {
        $type = \substr($type, (int) \strpos($type, '.') + 1); // Remove self.|enemy. prefix
        foreach ($this->resolvers as $resolver) {
            if ($resolver->supports($type)) {
                return $resolver->resolve($type, $entity);
            }
        }

        throw new UnsupportedTypeElementResolverException('Unsupported type: ' . $type);
    }
}
