<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Resolver;

use Velkuns\GameTextEngine\Exception\Core\UnsupportedValueResolverPropertyException;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInfo;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;
use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

readonly class EntityInfoResolver implements ResolverInterface
{
    private const string PATTERN = '#info\.?.*#';

    public function supports(string $type): bool
    {
        return \preg_match(self::PATTERN, $type) === 1;
    }

    public function resolve(string $type, EntityInterface $entity): EntityInfo
    {
        return $entity->getInfo();
    }

    /**
     * @param list<Modifier> $modifiers
     */
    public function resolveValue(string $type, EntityInterface $entity, array $modifiers = []): int
    {
        $info     = $this->resolve($type, $entity);
        $property = \substr($type, (int) \strrpos($type, '.') + 1);

        return match ($property) {
            'level' => $info->level,
            default => throw new UnsupportedValueResolverPropertyException("EntityInfo '$property' does not exist."),
        };
    }
}
