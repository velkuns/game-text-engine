<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rules\Player;

use Velkuns\GameTextEngine\Core\Evaluator\Evaluator;
use Velkuns\GameTextEngine\Exception\Rules\PlayerRulesException;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;

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

    public function getXpStep(Evaluator $evaluator, EntityInterface $entity): int
    {
        return (int) $evaluator->evaluate($this->rule, $entity);
    }

    public function assertMaxLevelNotReached(int $level): void
    {
        if ($level === $this->maxLevel) {
            throw new PlayerRulesException("You already reached the max level ($this->maxLevel)", 2300);
        }
    }

    public function assertHasEnoughXp(Evaluator $evaluator, EntityInterface $entity): void
    {
        $step = $this->getXpStep($evaluator, $entity);
        $diff = $step - $entity->getInfo()->xp;
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
