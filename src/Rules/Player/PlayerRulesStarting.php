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
 * @phpstan-type PlayerRulesStartingData array{
 *    level: int,
 *    xp: int,
 * }
 */
class PlayerRulesStarting implements \JsonSerializable
{
    public function __construct(
        public int $level,
        public int $xp,
    ) {}

    /**
     * @phpstan-return PlayerRulesStartingData
     */
    public function jsonSerialize(): array
    {
        return [
            'level' => $this->level,
            'xp'    => $this->xp,
        ];
    }
}
