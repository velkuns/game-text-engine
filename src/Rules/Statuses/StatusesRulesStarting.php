<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rules\Statuses;

/**
 * @phpstan-type StatusesRulesStartingData array{
 *    attributions: array<string, int>,
 * }
 */
class StatusesRulesStarting implements \JsonSerializable
{
    /**
     * @param array<string, int> $attributions
     */
    public function __construct(
        public array $attributions,
    ) {}

    /**
     * @phpstan-return StatusesRulesStartingData
     */
    public function jsonSerialize(): array
    {
        return [
            'attributions' => $this->attributions,
        ];
    }
}
