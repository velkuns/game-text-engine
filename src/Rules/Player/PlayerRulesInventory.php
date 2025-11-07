<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rules\Player;

/**
 * @phpstan-type PlayerRulesInventoryData array{
 *    limit: int,
 * }
 */
class PlayerRulesInventory implements \JsonSerializable
{
    public function __construct(
        public int $limit,
    ) {}

    /**
     * @phpstan-return PlayerRulesInventoryData
     */
    public function jsonSerialize(): array
    {
        return [
            'limit' => $this->limit,
        ];
    }
}
