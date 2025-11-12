<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rules\Attributes;

/**
 * @phpstan-type AttributesRulesStartingData array{
 *    attributionPoints: int,
 *    attributionPointsMaxPerAttribute: int,
 * }
 */
class AttributesRulesStarting implements \JsonSerializable
{
    public function __construct(
        public int $attributionPoints,
        public int $attributionPointsMaxPerAttribute,
    ) {}

    /**
     * @phpstan-return AttributesRulesStartingData
     */
    public function jsonSerialize(): array
    {
        return [
            'attributionPoints'                => $this->attributionPoints,
            'attributionPointsMaxPerAttribute' => $this->attributionPointsMaxPerAttribute,
        ];
    }
}
