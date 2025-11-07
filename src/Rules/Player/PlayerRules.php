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
 * @phpstan-import-type PlayerRulesStartingData from PlayerRulesStarting
 * @phpstan-import-type PlayerRulesLevelingData from PlayerRulesLeveling
 * @phpstan-import-type PlayerRulesInventoryData from PlayerRulesInventory
 * @phpstan-type PlayerRulesData array{
 *     description: string,
 *     starting: PlayerRulesStartingData,
 *     leveling: PlayerRulesLevelingData,
 *     inventory: PlayerRulesInventoryData,
 * }
 */
class PlayerRules implements \JsonSerializable
{
    public function __construct(
        public string $description,
        public PlayerRulesStarting $starting,
        public PlayerRulesLeveling $leveling,
        public PlayerRulesInventory $inventory,
    ) {}

    /**
     * @phpstan-return PlayerRulesData
     */
    public function jsonSerialize(): array
    {
        return [
            'description' => $this->description,
            'starting'    => $this->starting->jsonSerialize(),
            'leveling'    => $this->leveling->jsonSerialize(),
            'inventory'   => $this->inventory->jsonSerialize(),
        ];
    }
}
