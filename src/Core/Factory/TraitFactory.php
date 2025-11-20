<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Factory;

use Velkuns\GameTextEngine\Rpg\Trait\EntityTrait;
use Velkuns\GameTextEngine\Rpg\Trait\TraitInterface;

/**
 * @phpstan-import-type TraitData from TraitInterface
 */
readonly class TraitFactory
{
    public function __construct(
        private ModifierFactory $modifierFactory,
    ) {}

    /**
     * @param TraitData $data
     */
    public function from(array $data): TraitInterface
    {
        $type           = $data['type'];
        $name           = $data['name'];
        $description    = $data['description'];
        $modifiers      = \array_map(fn($modifier) => $this->modifierFactory->from($modifier), $data['modifiers']);

        return new EntityTrait($type, $name, $description, $modifiers);
    }
}
