<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Modifier;

use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Exception\ModifierException;
use Velkuns\GameTextEngine\Element\Resolver\TypeElementResolver;

readonly class ModifierProcessor
{
    public function __construct(
        private TypeElementResolver $typeElementResolver,
    ) {}

    public function apply(Modifier $modifier, EntityInterface $player, ?EntityInterface $enemy = null): void
    {
        //~ Some modifier are applied on the player, others on the enemy, depending on their type
        $entity = \str_starts_with($modifier->type, 'self') ? $player : $enemy;

        if ($entity === null) {
            throw new ModifierException('Cannot apply modifier: $enemy entity parameter is null', 1200);
        }

        $typePart = \explode('.', $modifier->type);
        $property = \array_pop($typePart); // Last part is "property" to modify
        $type     = \implode('.', $typePart);

        $element = $this->typeElementResolver->resolveAsObject($entity, $type);

        $this->process($element, $property, $modifier->value);

        return;
    }

    private function process(object $element, string $property, int $value): void
    {
        $reflectionClass = new \ReflectionClass($element);

        //~ Try access to the property directly
        if (
            $reflectionClass->hasProperty($property)
            && $reflectionClass->getProperty($property)->isPublic()
            && !$reflectionClass->getProperty($property)->isReadOnly()
            && \is_int($element->{$property})
        ) {
            $element->{$property} += $value;
            return;
        }

        throw new ModifierException('Cannot apply modifier: property not found, not accessible or not a integer', 1201);
    }
}
