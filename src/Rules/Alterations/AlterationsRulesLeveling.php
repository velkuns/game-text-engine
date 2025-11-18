<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rules\Alterations;

use Velkuns\GameTextEngine\Exception\Rules\AlterationsRulesException;

/**
 * @phpstan-type AlterationsRulesLevelingData array<string, array{
 *    everyNumberLevel?: int,
 *    number?: int,
 * }>
 */
class AlterationsRulesLeveling implements \JsonSerializable
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
     * @param array<string, list<string>> $alterations
     */
    public function assertHasCorrectAttribution(int $currentLevel, array $alterations): void
    {
        foreach (\array_keys($this->attributions) as $type) {
            $number            = $this->getNumber($type);
            $diff              = $number - \count($alterations[$type] ?? []);
            $hasNewAlterations = ($alterations[$type] ?? []) !== [];
            $canAttribute      = $this->canAttributeOnNextLevel($currentLevel, $type);

            if (!$canAttribute && $hasNewAlterations) {
                throw new AlterationsRulesException("New alterations only allowed every {$this->getEveryNumberLevel($type)} level(s).", 2400);
            }

            if ($canAttribute && $diff < 0) {
                throw new AlterationsRulesException("Only $number '$type' allowed, " . \abs($diff) . " given.", 2401);
            }

            if ($canAttribute && $diff > 0) {
                throw new AlterationsRulesException("Remaining $diff alteration(s) of '$type' to attribute.", 2402);
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
     * @phpstan-return AlterationsRulesLevelingData
     */
    public function jsonSerialize(): array
    {
        return $this->attributions;
    }
}
