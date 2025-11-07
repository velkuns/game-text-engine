<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rules\Player;

use Velkuns\GameTextEngine\Exception\Rules\PlayerRulesException;

/**
 * @phpstan-type PlayerRulesLevelingData array{
 *    rule: string,
 *    maxLevel: int,
 * }
 */
class PlayerRulesLeveling implements \JsonSerializable
{
    public function __construct(
        public string $rule,
        public int $maxLevel,
    ) {}

    public function getXpStep(int $level): int
    {
        try {
            $formula = \str_replace('level', (string) $level, $this->rule);
            $step    = 0;

            eval("\$step = $formula;");
        } catch (\Throwable) { // @codeCoverageIgnore
            $step = 0;         // @codeCoverageIgnore
        }

        return $step;
    }

    public function assertMaxLevelNotReached(int $level): void
    {
        if ($level === $this->maxLevel) {
            throw new PlayerRulesException("You already reached the max level ($this->maxLevel)", 2300);
        }
    }

    public function assertHasEnoughXp(int $level, int $xp): void
    {
        $step = $this->getXpStep($level);
        $diff = $step - $xp;
        if ($diff > 0) {
            throw new PlayerRulesException("You need $diff XP to reach the next level.", 2301);
        }
    }

    /**
     * @phpstan-return PlayerRulesLevelingData
     */
    public function jsonSerialize(): array
    {
        return [
            'rule'     => $this->rule,
            'maxLevel' => $this->maxLevel,
        ];
    }
}
