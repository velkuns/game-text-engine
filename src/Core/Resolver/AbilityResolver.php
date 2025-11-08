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
use Velkuns\GameTextEngine\Rpg\Ability\AbilityInterface;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;

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
