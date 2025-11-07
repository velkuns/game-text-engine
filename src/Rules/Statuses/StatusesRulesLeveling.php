<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rules\Statuses;

use Velkuns\GameTextEngine\Exception\Rules\StatusesRulesException;

/**
 * @phpstan-type StatusesRulesLevelingData array{
 *    everyNumberLevel: int,
 *    attributions: array<string, int>,
 * }
 */
class StatusesRulesLeveling implements \JsonSerializable
{
    /**
     * @param array<string, int> $attributions
     */
    public function __construct(
        public int $everyNumberLevel,
        public array $attributions,
    ) {}

    /**
     * @param array<string, list<string>> $statuses
     */
    public function assertCanAttributeOnNextLevel(int $currentLevel, array $statuses): void
    {
        $nextLevel = $currentLevel + 1;

        // As we start to level 1, use next - 1 for correct modulo
        if ($statuses !== [] && ($nextLevel - 1) % $this->everyNumberLevel > 0) {
            throw new StatusesRulesException("New statuses only allowed every $this->everyNumberLevel level(s).", 2200);
        }
    }

    /**
     * @param array<string, list<string>> $statuses
     */
    public function assertHasCorrectAttribution(array $statuses): void
    {
        foreach ($this->attributions as $type => $number) {
            $diff = $number - \count($statuses[$type] ?? []);

            if ($diff < 0) {
                throw new StatusesRulesException("Only $number '$type' allowed, " . \abs($diff) . " given.", 2201);
            }

            if ($diff > 0) {
                throw new StatusesRulesException("Remaining $diff status(es) of '$type' to attribute.", 2202);
            }
        }
    }

    /**
     * @phpstan-return StatusesRulesLevelingData
     */
    public function jsonSerialize(): array
    {
        return [
            'everyNumberLevel'  => $this->everyNumberLevel,
            'attributions'      => $this->attributions,
        ];
    }
}
