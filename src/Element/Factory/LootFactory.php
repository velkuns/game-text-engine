<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Factory;

use Velkuns\GameTextEngine\Element\Entity\EntityLoot;

/**
 * @phpstan-import-type EntityLootData from EntityLoot
 */
readonly class LootFactory
{
    /**
     * @phpstan-param EntityLootData|null $data
     */
    public function from(?array $data): EntityLoot
    {
        $coinsLoot = $data['coins'] ?? null;
        $itemsLoot = $data['items'] ?? null;

        return new EntityLoot($coinsLoot, $itemsLoot);
    }
}
