<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Resolver;

use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;
use Velkuns\GameTextEngine\Rpg\Entity\EntityAlterations;
use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

readonly class AlterationResolver implements ResolverInterface
{
    private const string PATTERN = '#alteration\.(?P<type>[a-z]+)#';

    public function supports(string $type): bool
    {
        return \preg_match(self::PATTERN, $type) === 1;
    }

    public function resolve(string $type, EntityInterface $entity): EntityAlterations
    {
        return $entity->getAlterations();
    }

    /**
     * @param list<Modifier> $modifiers
     * @codeCoverageIgnore
     */
    public function resolveValue(string $type, EntityInterface $entity, array $modifiers = []): int
    {
        return 0;
    }
}
