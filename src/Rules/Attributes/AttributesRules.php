<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rules\Attributes;

use Velkuns\GameTextEngine\Rpg\Attribute\SimpleAttribute;
use Velkuns\GameTextEngine\Rpg\Attribute\CompoundAttribute;

/**
 * @phpstan-import-type SimpleAttributeData from SimpleAttribute
 * @phpstan-import-type CompoundAttributeData from CompoundAttribute
 * @phpstan-import-type AttributesRulesStartingData from AttributesRulesStarting
 * @phpstan-import-type AttributesRulesLevelingData from AttributesRulesLeveling
 * @phpstan-type AttributesRulesData array{
 *    description: string,
 *    starting: AttributesRulesStartingData,
 *    leveling: AttributesRulesLevelingData,
 *    simples: array<string, SimpleAttributeData>,
 *    compounds: array<string, CompoundAttributeData>,
 * }
 */
class AttributesRules implements \JsonSerializable
{
    /**
     * @param array<string, SimpleAttribute> $simpleAttributes
     * @param array<string, CompoundAttribute> $compoundAttributes
     */
    public function __construct(
        public string $description,
        public AttributesRulesStarting $starting,
        public AttributesRulesLeveling $leveling,
        public array $simpleAttributes = [],
        public array $compoundAttributes = [],
    ) {}

    /**
     * @phpstan-return AttributesRulesData
     */
    public function jsonSerialize(): array
    {
        //~ Before dump, we need to reset initial/max/value for attribute with init rule.
        $simpleAttributes = $this->simpleAttributes;
        foreach ($simpleAttributes as $attribute) {
            if ($attribute->getRule() === null) {
                continue;
            }

            $attribute->value   = 0;
            $attribute->initial = 0;
            $attribute->max     = 0;
        }

        return [
            'description' => $this->description,
            'starting'    => $this->starting->jsonSerialize(),
            'leveling'    => $this->leveling->jsonSerialize(),
            'simples'       => \array_map(fn(SimpleAttribute $attribute) => $attribute->jsonSerialize(), $simpleAttributes),
            'compounds'   => \array_map(fn(CompoundAttribute $attribute) => $attribute->jsonSerialize(), $this->compoundAttributes),
        ];
    }
}
