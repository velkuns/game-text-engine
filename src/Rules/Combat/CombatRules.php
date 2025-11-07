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
 * @phpstan-import-type CombatRulesHitData from CombatRulesHit
 * @phpstan-import-type CombatRulesLevelingData from CombatRulesLeveling
 * @phpstan-type CombatRulesData array{
 *    description: string,
 *    hit: CombatRulesHitData,
 *    leveling: CombatRulesLevelingData,
 * }
 */
class CombatRules implements \JsonSerializable
{
    public function __construct(
        public string $description,
        public CombatRulesHit $hit,
        public CombatRulesLeveling $leveling,
    ) {}

    /**
     * @phpstan-return CombatRulesData
     */
    public function jsonSerialize(): array
    {
        return [
            'description' => $this->description,
            'hit'         => $this->hit->jsonSerialize(),
            'leveling'    => $this->leveling->jsonSerialize(),
        ];
    }
}
