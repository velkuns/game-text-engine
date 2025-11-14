<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Factory;

use Velkuns\GameTextEngine\Rpg\Attribute\SimpleAttribute;
use Velkuns\GameTextEngine\Rpg\Attribute\CompoundAttribute;
use Velkuns\GameTextEngine\Rpg\Attribute\ConstraintsAttribute;

/**
 * @phpstan-import-type SimpleAttributeData from SimpleAttribute
 * @phpstan-import-type CompoundAttributeData from CompoundAttribute
 */
readonly class AttributeFactory
{
    /**
     * @param array<string, SimpleAttributeData> $data
     * @return array<string, SimpleAttribute>
     */
    public function fromSimples(array $data): array
    {
        $withoutInit = \array_filter($data, fn(array $attributeData) => $attributeData['rule'] === null);
        $withInit    = \array_filter($data, fn(array $attributeData) => $attributeData['rule'] !== null);

        //~ First create all simple attributes without initialization (no rule)
        $simples = \array_map(fn(array $attributeData) => $this->fromSimpleAttribute($attributeData), $withoutInit);

        //~ Then create all simple attributes with init (with rule for initialization)
        $simples += \array_map(fn(array $attributeData) => $this->fromSimpleAttribute($attributeData, $simples), $withInit);

        return $simples;
    }

    /**
     * @phpstan-param array<string, CompoundAttributeData> $data
     * @phpstan-param array<string, SimpleAttribute> $simples
     * @return array<string, CompoundAttribute>
     */
    public function fromCompounds(array $data, array $simples): array
    {
        return \array_map(fn(array $attributeData) => $this->fromCompoundAttribute($attributeData, $simples), $data);
    }

    /**
     * @phpstan-param SimpleAttributeData $data
     * @phpstan-param array<string, SimpleAttribute> $simpleAttributes
     */
    public function fromSimpleAttribute(array $data, array $simpleAttributes = []): SimpleAttribute
    {
        //~ Filter only related attributes
        $attributes = \array_filter(
            $simpleAttributes,
            fn(SimpleAttribute $attribute) => \str_contains($data['rule'] ?? '', $attribute->name),
        );

        return new SimpleAttribute(
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
     * @phpstan-param array<string, SimpleAttribute> $simpleAttributes
     */
    public function fromCompoundAttribute(array $data, array $simpleAttributes): CompoundAttribute
    {
        return new CompoundAttribute(
            name: $data['name'],
            rule: $data['rule'],
            attributes: $simpleAttributes,
        );
    }
}
