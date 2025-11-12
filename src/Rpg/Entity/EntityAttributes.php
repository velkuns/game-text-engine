<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Entity;

use Velkuns\GameTextEngine\Rpg\Attribute\AttributeInterface;
use Velkuns\GameTextEngine\Rpg\Attribute\BaseAttribute;
use Velkuns\GameTextEngine\Rpg\Attribute\CompoundAttribute;

/**
 * @phpstan-import-type BaseAttributeData from BaseAttribute
 * @phpstan-import-type CompoundAttributeData from CompoundAttribute
 * @phpstan-type AttributesData array{
 *     bases: array<string, BaseAttributeData>,
 *     compounds: array<string, CompoundAttributeData>,
 * }
 */
readonly class EntityAttributes implements \JsonSerializable
{
    /**
     * @param array<string, BaseAttribute> $bases
     * @param array<string, CompoundAttribute> $compounds
     */
    public function __construct(
        public array $bases,
        public array $compounds = [],
    ) {}

    public function get(string $name): ?AttributeInterface
    {
        return $this->bases[$name] ?? $this->compounds[$name] ?? null;
    }

    /**
     * @return AttributesData
     */
    public function jsonSerialize(): array
    {
        return [
            'bases'     => \array_map(fn(AttributeInterface $attribute) => $attribute->jsonSerialize(), $this->bases),
            'compounds' => \array_map(fn(AttributeInterface $attribute) => $attribute->jsonSerialize(), $this->compounds),
        ];
    }

    public function clone(): self
    {
        $bases     = \array_map(fn(AttributeInterface $attribute) => $attribute->clone(), $this->bases);
        $compounds = \array_map(fn(AttributeInterface $attribute) => $attribute->clone($bases), $this->compounds);

        return new self(
            $bases,
            $compounds,
        );
    }
}
