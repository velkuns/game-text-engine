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
use Velkuns\GameTextEngine\Rpg\Attribute\SimpleAttribute;
use Velkuns\GameTextEngine\Rpg\Attribute\CompoundAttribute;

/**
 * @phpstan-import-type SimpleAttributeData from SimpleAttribute
 * @phpstan-import-type CompoundAttributeData from CompoundAttribute
 * @phpstan-type AttributesData array{
 *     simples: array<string, SimpleAttributeData>,
 *     compounds: array<string, CompoundAttributeData>,
 * }
 */
readonly class EntityAttributes implements \JsonSerializable
{
    /**
     * @param array<string, SimpleAttribute> $simples
     * @param array<string, CompoundAttribute> $compounds
     */
    public function __construct(
        public array $simples,
        public array $compounds = [],
    ) {}

    public function get(string $name): ?AttributeInterface
    {
        return $this->simples[$name] ?? $this->compounds[$name] ?? null;
    }

    /**
     * @return AttributesData
     */
    public function jsonSerialize(): array
    {
        return [
            'simples'     => \array_map(fn(AttributeInterface $attribute) => $attribute->jsonSerialize(), $this->simples),
            'compounds' => \array_map(fn(AttributeInterface $attribute) => $attribute->jsonSerialize(), $this->compounds),
        ];
    }

    public function clone(): self
    {
        $simples     = \array_map(fn(AttributeInterface $attribute) => $attribute->clone(), $this->simples);
        $compounds = \array_map(fn(AttributeInterface $attribute) => $attribute->clone($simples), $this->compounds);

        return new self(
            $simples,
            $compounds,
        );
    }
}
