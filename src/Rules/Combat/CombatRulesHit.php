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
 * @phpstan-import-type CombatRulesHitDetailData from CombatRulesHitDetail
 * @phpstan-type CombatRulesHitData array{
 *     chance: CombatRulesHitDetailData,
 *     roll: CombatRulesHitDetailData,
 *     damages: CombatRulesHitDetailData,
 * }
 */
class CombatRulesHit implements \JsonSerializable
{
    public function __construct(
        public CombatRulesHitDetail $chance,
        public CombatRulesHitDetail $roll,
        public CombatRulesHitDetail $damages,
    ) {}

    /**
     * @phpstan-return CombatRulesHitData
     */
    public function jsonSerialize(): array
    {
        return [
            'chance'  => $this->chance->jsonSerialize(),
            'roll'    => $this->roll->jsonSerialize(),
            'damages' => $this->damages->jsonSerialize(),
        ];
    }
}
