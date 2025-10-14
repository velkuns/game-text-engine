<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Factory;

use Velkuns\GameTextEngine\Element\Item\Item;

/**
 * @phpstan-import-type ItemData from Item
 */
readonly class ItemFactory
{
    public function __construct(
        private ModifierFactory $modifierFactory,
    ) {}

    /**
     * @param ItemData $data
     */
    public function from(array $data): Item
    {
        $name        = $data['name'];
        $subType     = $data['subType'] ?? null;
        $description = $data['description'];
        $modifiers   = \array_map(fn($modifier) => $this->modifierFactory->from($modifier), $data['modifiers']);
        $flags       = $data['flags'];
        $equipped    = $data['equipped'];
        $price       = $data['price'];

        return new Item($name, $subType, $description, $modifiers, $flags, $equipped, $price);
    }
}
