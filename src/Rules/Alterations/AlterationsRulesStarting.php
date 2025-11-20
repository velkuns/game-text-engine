<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rules\Alterations;

/**
 * @phpstan-type AlterationsRulesStartingData array<string, array{
 *    number?: int
 * }>
 */
class AlterationsRulesStarting implements \JsonSerializable
{
    /**
     * @param array<string, array<string, int>> $attributions
     */
    public function __construct(
        public array $attributions,
    ) {}

    public function getNumber(string $type): int
    {
        return $this->attributions[$type]['number'] ?? 0;
    }

    /**
     * @phpstan-return list<string>
     */
    public function getAllTypes(): array
    {
        return \array_keys($this->attributions);
    }

    /**
     * @phpstan-return AlterationsRulesStartingData
     */
    public function jsonSerialize(): array
    {
        return $this->attributions;
    }
}
