<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rules\Combat;

/**
 * @phpstan-type CombatRulesLevelingData array{
 *    xpGainDefault: int,
 *    xpGainBonusPerLevel: int,
 * }
 */
class CombatRulesLeveling implements \JsonSerializable
{
    public function __construct(
        public int $xpGainDefault,
        public int $xpGainBonusPerLevel,
    ) {}

    /**
     * @phpstan-return CombatRulesLevelingData
     */
    public function jsonSerialize(): array
    {
        return [
            'xpGainDefault'       => $this->xpGainDefault,
            'xpGainBonusPerLevel' => $this->xpGainBonusPerLevel,
        ];
    }
}
