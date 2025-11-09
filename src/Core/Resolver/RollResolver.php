<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Resolver;

use Random\Randomizer;
use Velkuns\GameTextEngine\Exception\Core\UnsupportedValueResolverPropertyException;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;
use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

readonly class RollResolver implements ResolverInterface
{
    private const string PATTERN = '#roll\((?P<roll>[0-9]+)\)#';

    public function __construct(
        private Randomizer $randomizer,
    ) {}

    public function supports(string $type): bool
    {
        return \preg_match(self::PATTERN, $type) === 1;
    }

    public function resolve(string $type, EntityInterface $entity): Randomizer
    {
        return $this->randomizer;
    }

    /**
     * @param list<Modifier> $modifiers
     */
    public function resolveValue(string $type, EntityInterface $entity, array $modifiers = []): int|float
    {
        $randomizer  = $this->resolve($type, $entity);
        $found = preg_match(self::PATTERN, $type, $match);

        if ($found === 0 || $found === false) {
            //~ Should not happen, as the test pass on supports() method
            throw new UnsupportedValueResolverPropertyException("roll(max) need to have max defined."); // @codeCoverageIgnore
        }

        $max = (float) $match['roll'];

        if ($max === 1.0) {
            return $randomizer->getFloat(0, $max);
        }

        return $randomizer->getInt(0, (int) $max);
    }
}
