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
use Velkuns\GameTextEngine\Element\Item\ItemInterface;

/**
 * @phpstan-import-type ItemData from ItemInterface
 */
readonly class ItemFactory
{
    public function __construct(
        private ModifierFactory $modifierFactory,
    ) {}

    /**
     * @param ItemData $data
     */
    public function from(array $data): ItemInterface
    {
        $name        = $data['name'];
        $subType     = $data['subType'] ?? null;
        $description = $data['description'];
        $modifiers   = \array_map(fn($modifier) => $this->modifierFactory->from($modifier), $data['modifiers']);
        $flags       = $data['flags'];
        $equipped    = $data['equipped'];
        $damages     = $data['damages'];
        $price       = $data['price'];

        return new Item($name, $subType, $description, $modifiers, $flags, $equipped, $damages, $price);
    }
}
