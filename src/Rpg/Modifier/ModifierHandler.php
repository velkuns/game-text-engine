<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Modifier;

use Velkuns\GameTextEngine\Core\Resolver\TypeElementResolverHandler;
use Velkuns\GameTextEngine\Exception\Rpg\ModifierException;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;

readonly class ModifierHandler
{
    /**
     * @param list<ModifierProcessInterface> $processors
     */
    public function __construct(
        private TypeElementResolverHandler $typeElementResolverHandler,
        private array $processors = [],
    ) {}

    public function handle(Modifier $modifier, EntityInterface $player, ?EntityInterface $enemy = null): void
    {
        //~ Some modifier are applied on the player, others on the enemy, depending on their type
        $entity = \str_starts_with($modifier->type, 'self') ? $player : $enemy;

        if ($entity === null) {
            throw new ModifierException('Cannot apply modifier: $enemy entity parameter is null', 1200);
        }

        $element = $this->typeElementResolverHandler->handle($modifier->type, $entity);

        $type = \substr($modifier->type, (int) \strpos($modifier->type, '.') + 1); // Remove self.|enemy. prefix

        foreach ($this->processors as $processor) {
            if ($processor->supports($type)) {
                $processor->apply($type, $element, $modifier->value);
            }
        }
    }
}
