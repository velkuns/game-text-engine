<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Resolver;

use Velkuns\GameTextEngine\Element\Ability\AbilityInterface;
use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Exception\Element\ResolverException;

readonly class AbilityResolver implements ResolverInterface
{
    private const string PATTERN = '#ability\.(?P<name>[a-z]+)#';

    public function supports(string $type): bool
    {
        return \preg_match(self::PATTERN, $type) === 1;
    }

    public function resolve(string $type, EntityInterface $entity): AbilityInterface
    {
        \preg_match(self::PATTERN, $type, $matches);

        $name    = $matches['name'] ?? '';
        $ability = $entity->getAbilities()->get($name);

        if ($ability === null) {
            throw new ResolverException("Ability '$name' not found.");
        }

        return $ability;
    }
}
