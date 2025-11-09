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
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;
use Velkuns\GameTextEngine\Rpg\Item\ItemInterface;
use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

readonly class EquippedWeaponItemResolver implements ResolverInterface
{
    private const string PATTERN = '#weapon\.equipped#';

    public function supports(string $type): bool
    {
        return \preg_match(self::PATTERN, $type) === 1;
    }

    public function resolve(string $type, EntityInterface $entity): ItemInterface
    {
        $item = $entity->getInventory()->getEquippedWeapon();
        if ($item === null) {
            throw new ResolverException('No weapon equipped.');
        }

        return $item;
    }

    /**
     * @param list<Modifier> $modifiers
     */
    public function resolveValue(string $type, EntityInterface $entity, array $modifiers = []): int
    {
        try {
            $item = $this->resolve($type, $entity);
        } catch (ResolverException) {
            return 0;
        }

        $property = \substr($type, (int) \strrpos($type, '.') + 1);

        return match ($property) {
            'damages'                => $item->getDamages()->getTotal(),
            'damages_with_modifiers' => $item->getDamages()->getTotalWithModifiers($modifiers),
            default   => throw new UnsupportedValueResolverPropertyException("Item '$property' does not exist."),
        };
    }
}
