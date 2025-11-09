<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Resolver;

use Velkuns\GameTextEngine\Exception\Core\ResolverException;
use Velkuns\GameTextEngine\Exception\Core\UnsupportedValueResolverPropertyException;
use Velkuns\GameTextEngine\Exception\Rpg\DamageException;
use Velkuns\GameTextEngine\Rpg\Damages\DamagesDetailInterface;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;

readonly class EntityDamagesResolver implements ResolverInterface
{
    private const string PATTERN = '#damages\.(?P<type>[a-z]+)#';

    public function supports(string $type): bool
    {
        return \preg_match(self::PATTERN, $type) === 1;
    }

    public function resolve(string $type, EntityInterface $entity): DamagesDetailInterface
    {
        \preg_match(self::PATTERN, $type, $matches);

        $damagesType = $matches['type'] ?? '';

        try {
            $damages = $entity->getDamages()->get($damagesType);
        } catch (DamageException) {
            throw new ResolverException("Damages with type '$damagesType' not found.");
        }

        return $damages;
    }

    public function resolveValue(string $type, EntityInterface $entity, array $modifiers = []): int
    {
        $damagesDetail = $this->resolve($type, $entity);
        $property      = \substr($type, (int) \strrpos($type, '.') + 1);

        return match ($property) {
            'value'                => $damagesDetail->getValue(),
            'value_with_modifiers' => $damagesDetail->getValueWithModifiers($modifiers),
            default => throw new UnsupportedValueResolverPropertyException("Damages detail '$property' does not exist."),
        };
    }
}
