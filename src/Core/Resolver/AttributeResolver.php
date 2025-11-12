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
use Velkuns\GameTextEngine\Rpg\Attribute\AttributeInterface;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;
use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

readonly class AttributeResolver implements ResolverInterface
{
    private const string PATTERN = '#attribute\.(?P<name>[a-z]+)#';

    public function supports(string $type): bool
    {
        return \preg_match(self::PATTERN, $type) === 1;
    }

    public function resolve(string $type, EntityInterface $entity): AttributeInterface
    {
        \preg_match(self::PATTERN, $type, $matches);

        $name    = $matches['name'] ?? '';
        $attribute = $entity->getAttributes()->get($name);

        if ($attribute === null) {
            throw new ResolverException("Attribute '$name' not found.");
        }

        return $attribute;
    }

    /**
     * @param list<Modifier> $modifiers
     */
    public function resolveValue(string $type, EntityInterface $entity, array $modifiers = []): int
    {
        $attribute  = $this->resolve($type, $entity);
        $property   = \substr($type, (int) \strrpos($type, '.') + 1);

        return match ($property) {
            'value'                => $attribute->getValue(),
            'value_with_modifiers' => $attribute->getValueWithModifiers($modifiers),
            default => throw new UnsupportedValueResolverPropertyException("Attribute '$property' does not exist."),
        };
    }
}
