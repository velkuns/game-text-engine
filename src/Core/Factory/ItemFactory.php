<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Factory;

use Velkuns\GameTextEngine\Rpg\Item\Item;
use Velkuns\GameTextEngine\Rpg\Item\ItemInterface;

/**
 * @phpstan-import-type ItemData from ItemInterface
 */
readonly class ItemFactory
{
    public function __construct(
        private ModifierFactory $modifierFactory,
        private DamageFactory $damageFactory,
    ) {}

    /**
     * @phpstan-param ItemData $data
     */
    public function from(array $data): ItemInterface
    {
        $name        = $data['name'];
        $subType     = $data['subType'] ?? null;
        $description = $data['description'];
        $modifiers   = \array_map(fn($modifier) => $this->modifierFactory->from($modifier), $data['modifiers']);
        $flags       = $data['flags'];
        $equipped    = $data['equipped'];
        $damages     = $this->damageFactory->fromDamages($data['damages'] ?? []);
        $quantity    = $data['quantity'] ?? 1;
        $price       = $data['price'];

        return new Item($name, $subType, $description, $modifiers, $flags, $equipped, $damages, $quantity, $price);
    }
}
