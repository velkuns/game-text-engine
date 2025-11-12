<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rules\Attributes;

use Velkuns\GameTextEngine\Exception\Rules\AttributesRulesException;

/**
 * @phpstan-type AttributesRulesLevelingData array{
 *    attributionPoints: int,
 *    attributionPointsMaxPerAttribute: int,
 * }
 */
class AttributesRulesLeveling implements \JsonSerializable
{
    public function __construct(
        public int $attributionPoints,
        public int $attributionPointsMaxPerAttribute,
    ) {}

    /**
     * @param array<string, int> $attributes
     */
    public function assertHasCorrectAttribution(array $attributes): void
    {
        $total = 0;
        foreach ($attributes as $value) {
            if ($value > $this->attributionPointsMaxPerAttribute) {
                throw new AttributesRulesException("Only $this->attributionPointsMaxPerAttribute point(s) per attribute is allowed for level up.", 2100);
            }

            $total += $value;
        }

        $diff = $this->attributionPoints - $total;
        if ($diff < 0) {
            throw new AttributesRulesException("Only $this->attributionPoints point(s) is allowed for level up.", 2101);
        }

        if ($diff > 0) {
            throw new AttributesRulesException("Remaining $diff point(s) to attribute to attributes", 2102);
        }
    }

    /**
     * @phpstan-return AttributesRulesLevelingData
     */
    public function jsonSerialize(): array
    {
        return [
            'attributionPoints'                => $this->attributionPoints,
            'attributionPointsMaxPerAttribute' => $this->attributionPointsMaxPerAttribute,
        ];
    }
}
