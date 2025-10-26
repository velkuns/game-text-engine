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
use Velkuns\GameTextEngine\Element\Exception\TypeElementResolveException;

readonly class TypeElementResolver
{
    /**
     * @return \Countable&iterable<object>
     */
    public function resolve(EntityInterface $entity, string $type): iterable
    {
        $from = \explode('.', $type);

        \array_shift($from); // Remove 'self' or 'enemy'

        $element = $entity;

        while ($from !== []) {

            $property = \array_shift($from);

            //~ Pre-check element type
            if (!\is_object($element) && !\is_iterable($element)) {
                throw new TypeElementResolveException('Invalid "condition.type" (should end with an object or array element)', 1100);
            }

            //~ Check for array
            if (\is_array($element)) {
                if (!\array_key_exists($property, $element)) {
                    throw new TypeElementResolveException('Invalid "condition.type" (array key does not exist)', 1101); // @codeCoverageIgnore
                }
                $element = $element[$property];
                continue;
            }

            $reflectionClass = new \ReflectionClass($element);

            //~ check for property existence
            if ($reflectionClass->hasProperty($property) && $reflectionClass->getProperty($property)->isPublic()) {
                $element = $element->{$property};
                continue;
            }

            //~ Try with "get{Property}()" method
            $getterMethod = 'get' . \ucfirst($property);
            if ($reflectionClass->hasMethod($getterMethod) && $reflectionClass->getMethod($getterMethod)->isPublic()) {
                // @codeCoverageIgnoreStart
                $element = $element->{$getterMethod}();
                continue;
                // @codeCoverageIgnoreEnd
            }

            //~ Try with simple "get" method (for collections)
            if ($reflectionClass->hasMethod('get') && $reflectionClass->getMethod('get')->isPublic()) {
                $element = $element->get($property);
                continue;
            }

            throw new TypeElementResolveException('Invalid "condition.type" (cannot find property)', 1102);
        }

        if ($element === $entity) {
            throw new TypeElementResolveException('Invalid "condition.type" (not enough part)', 1103);
        }

        //~ Pre-check element type
        if (!\is_object($element) && !\is_iterable($element) && !\is_countable($element)) {
            throw new TypeElementResolveException('Invalid "condition.type" (should end with an object or array element)', 1104);
        }

        /** @var \Countable&iterable<object> $elements */
        $elements = \is_iterable($element) && \is_countable($element) ? $element : [$element];

        return $elements;
    }

    /**
     * @return object
     */
    public function resolveAsObject(EntityInterface $entity, string $type): object
    {
        $iterable = $this->resolve($entity, $type);

        if (\count($iterable) > 1 || count($iterable) === 0) {
            throw new TypeElementResolveException('Element not found or not unique', 1110);
        }

        $object = \current($iterable);

        if ($object === false) {
            throw new TypeElementResolveException('Element not found', 1111); // @codeCoverageIgnore
        }

        return $object;
    }
}
