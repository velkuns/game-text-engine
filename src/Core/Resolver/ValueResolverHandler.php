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
use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

readonly class ValueResolverHandler
{
    /**
     * @param list<ResolverInterface> $valueResolvers
     */
    public function __construct(
        private array $valueResolvers = [],
    ) {}

    /**
     * @param list<Modifier> $modifiers
     */
    public function handle(string $type, EntityInterface $entity, array $modifiers = []): int|float|bool
    {
        $type = \substr($type, (int) \strpos($type, '.') + 1); // Remove self.|enemy. prefix
        foreach ($this->valueResolvers as $resolver) {
            if ($resolver->supports($type)) {
                return $resolver->resolveValue($type, $entity, $modifiers);
            }
        }

        throw new UnsupportedTypeElementResolverException('Unsupported type: ' . $type);
    }
}
