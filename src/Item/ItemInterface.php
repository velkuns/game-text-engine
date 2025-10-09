<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Item;

use Velkuns\GameTextEngine\Modifier\Modifier;

interface ItemInterface
{
    public function getId(): int;
    public function getName(): string;
    public function getDescription(): string;

    /**
     * @return list<Modifier>
     */
    public function getModifiers(): array;

    public function isConsumable(): bool;
    public function isEquipable(): bool;
    public function isWeapon(): bool;
    public function isGear(): bool;
}
