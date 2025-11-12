<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Factory;

use Velkuns\GameTextEngine\Rpg\Attribute\BaseAttribute;
use Velkuns\GameTextEngine\Rpg\Attribute\CompoundAttribute;
use Velkuns\GameTextEngine\Rpg\Attribute\ConstraintsAttribute;

/**
 * @phpstan-import-type BaseAttributeData from BaseAttribute
 * @phpstan-import-type CompoundAttributeData from CompoundAttribute
 */
readonly class AttributeFactory
{
    /**
     * @param array<string, BaseAttributeData> $data
     * @return array<string, BaseAttribute>
     */
    public function fromBases(array $data): array
    {
        $withoutInit = \array_filter($data, fn(array $attributeData) => $attributeData['rule'] === null);
        $withInit    = \array_filter($data, fn(array $attributeData) => $attributeData['rule'] !== null);

        //~ First create all base attributes without initialization (no rule)
        $bases = \array_map(fn(array $attributeData) => $this->fromBaseAttribute($attributeData), $withoutInit);

        //~ Then create all base attributes with init (with rule for initialization)
        $bases += \array_map(fn(array $attributeData) => $this->fromBaseAttribute($attributeData, $bases), $withInit);

        return $bases;
    }

    /**
     * @phpstan-param array<string, CompoundAttributeData> $data
     * @phpstan-param array<string, BaseAttribute> $bases
     * @return array<string, CompoundAttribute>
     */
    public function fromCompounds(array $data, array $bases): array
    {
        return \array_map(fn(array $attributeData) => $this->fromCompoundAttribute($attributeData, $bases), $data);
    }

    /**
     * @phpstan-param BaseAttributeData $data
     * @phpstan-param array<string, BaseAttribute> $baseAttributes
     */
    public function fromBaseAttribute(array $data, array $baseAttributes = []): BaseAttribute
    {
        //~ Filter only related attributes
        $attributes = \array_filter(
            $baseAttributes,
            fn(BaseAttribute $attribute) => \str_contains($data['rule'] ?? '', $attribute->name),
        );

        return new BaseAttribute(
            name: $data['name'],
            value: $data['value'],
            max: $data['max'],
            constraints: new ConstraintsAttribute(
                min: $data['constraints']['min'],
                max: $data['constraints']['max'],
            ),
            initial: $data['initial'],
            rule: $data['rule'] ?? null,
            attributes: $attributes,
        );
    }

    /**
     * @phpstan-param CompoundAttributeData $data
     * @phpstan-param array<string, BaseAttribute> $baseAttributes
     */
    public function fromCompoundAttribute(array $data, array $baseAttributes): CompoundAttribute
    {
        return new CompoundAttribute(
            name: $data['name'],
            rule: $data['rule'],
            attributes: $baseAttributes,
        );
    }
}
