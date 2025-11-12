<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rules\Attributes;

use Velkuns\GameTextEngine\Rpg\Attribute\BaseAttribute;
use Velkuns\GameTextEngine\Rpg\Attribute\CompoundAttribute;

/**
 * @phpstan-import-type BaseAttributeData from BaseAttribute
 * @phpstan-import-type CompoundAttributeData from CompoundAttribute
 * @phpstan-import-type AttributesRulesStartingData from AttributesRulesStarting
 * @phpstan-import-type AttributesRulesLevelingData from AttributesRulesLeveling
 * @phpstan-type AttributesRulesData array{
 *    description: string,
 *    starting: AttributesRulesStartingData,
 *    leveling: AttributesRulesLevelingData,
 *    bases: array<string, BaseAttributeData>,
 *    compounds: array<string, CompoundAttributeData>,
 * }
 */
class AttributesRules implements \JsonSerializable
{
    /**
     * @param array<string, BaseAttribute> $baseAttributes
     * @param array<string, CompoundAttribute> $compoundAttributes
     */
    public function __construct(
        public string $description,
        public AttributesRulesStarting $starting,
        public AttributesRulesLeveling $leveling,
        public array $baseAttributes = [],
        public array $compoundAttributes = [],
    ) {}

    /**
     * @phpstan-return AttributesRulesData
     */
    public function jsonSerialize(): array
    {
        //~ Before dump, we need to reset initial/max/value for attribute with init rule.
        $baseAttributes = $this->baseAttributes;
        foreach ($baseAttributes as $attribute) {
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
            'bases'       => \array_map(fn(BaseAttribute $attribute) => $attribute->jsonSerialize(), $baseAttributes),
            'compounds'   => \array_map(fn(CompoundAttribute $attribute) => $attribute->jsonSerialize(), $this->compoundAttributes),
        ];
    }
}
