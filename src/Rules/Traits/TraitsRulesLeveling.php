<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rules\Traits;

use Velkuns\GameTextEngine\Exception\Rules\TraitsRulesException;

/**
 * @phpstan-type TraitsRulesLevelingData array{
 *    everyNumberLevel: int,
 *    attributions: array<string, int>,
 * }
 */
class TraitsRulesLeveling implements \JsonSerializable
{
    /**
     * @param array<string, int> $attributions
     */
    public function __construct(
        public int $everyNumberLevel,
        public array $attributions,
    ) {}

    /**
     * @param array<string, list<string>> $traits
     */
    public function assertCanAttributeOnNextLevel(int $currentLevel, array $traits): void
    {
        $nextLevel = $currentLevel + 1;

        // As we start to level 1, use next - 1 for correct modulo
        if ($traits !== [] && ($nextLevel - 1) % $this->everyNumberLevel > 0) {
            throw new TraitsRulesException("New traits only allowed every $this->everyNumberLevel level(s).", 2200);
        }
    }

    /**
     * @param array<string, list<string>> $traits
     */
    public function assertHasCorrectAttribution(array $traits): void
    {
        foreach ($this->attributions as $type => $number) {
            $diff = $number - \count($traits[$type] ?? []);

            if ($diff < 0) {
                throw new TraitsRulesException("Only $number '$type' allowed, " . \abs($diff) . " given.", 2201);
            }

            if ($diff > 0) {
                throw new TraitsRulesException("Remaining $diff trait(s) of '$type' to attribute.", 2202);
            }
        }
    }

    /**
     * @phpstan-return TraitsRulesLevelingData
     */
    public function jsonSerialize(): array
    {
        return [
            'everyNumberLevel'  => $this->everyNumberLevel,
            'attributions'      => $this->attributions,
        ];
    }
}
