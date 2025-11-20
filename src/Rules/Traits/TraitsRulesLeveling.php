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
 * @phpstan-type TraitsRulesLevelingData array<string, array{
 *    everyNumberLevel?: int,
 *    number?: int,
 * }>
 */
class TraitsRulesLeveling implements \JsonSerializable
{
    /**
     * @param array<string, array<string, int>> $attributions
     */
    public function __construct(
        public array $attributions,
    ) {}

    public function getEveryNumberLevel(string $type): int
    {
        return $this->attributions[$type]['everyNumberLevel'] ?? 1;
    }

    public function getNumber(string $type): int
    {
        return $this->attributions[$type]['number'] ?? 0;
    }

    /**
     * @param array<string, list<string>> $traits
     */
    public function assertHasCorrectAttribution(int $currentLevel, array $traits): void
    {
        foreach (\array_keys($this->attributions) as $type) {
            $number       = $this->getNumber($type);
            $diff         = $number - \count($traits[$type] ?? []);
            $hasNewTraits = ($traits[$type] ?? []) !== [];
            $canAttribute = $this->canAttributeOnNextLevel($currentLevel, $type);

            if (!$canAttribute && $hasNewTraits) {
                throw new TraitsRulesException("New traits only allowed every {$this->getEveryNumberLevel($type)} level(s).", 2200);
            }

            if ($canAttribute && $diff < 0) {
                throw new TraitsRulesException("Only $number '$type' allowed, " . \abs($diff) . " given.", 2201);
            }

            if ($canAttribute && $diff > 0) {
                throw new TraitsRulesException("Remaining $diff trait(s) of '$type' to attribute.", 2202);
            }
        }
    }

    private function canAttributeOnNextLevel(int $currentLevel, string $type): bool
    {
        $nextLevel        = $currentLevel + 1;
        $everyNumberLevel = $this->getEveryNumberLevel($type);

        return (($nextLevel - 1) % $everyNumberLevel) === 0;
    }

    /**
     * @phpstan-return TraitsRulesLevelingData
     */
    public function jsonSerialize(): array
    {
        return $this->attributions;
    }
}
