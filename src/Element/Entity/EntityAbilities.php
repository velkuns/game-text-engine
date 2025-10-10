<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Entity;

use Velkuns\GameTextEngine\Element\Ability\AbilityInterface;
use Velkuns\GameTextEngine\Element\Ability\BaseAbility;
use Velkuns\GameTextEngine\Element\Ability\CompoundAbility;

/**
 * @phpstan-import-type BaseAbilityData from BaseAbility
 * @phpstan-import-type CompoundAbilityData from CompoundAbility
 * @phpstan-type AbilitiesData array{
 *     base: array<string, BaseAbilityData>,
 *     compound: array<string, CompoundAbilityData>,
 * }
 */
class EntityAbilities implements \JsonSerializable
{
    /** @var array<string, BaseAbility> $base */
    public array $base = [];

    /** @var array<string, CompoundAbility> $compound */
    public array $compound = [];

    /**
     * @param list<BaseAbility> $baseAbilities
     * @param list<CompoundAbility> $compoundAbilities
     */
    public function __construct(
        array $baseAbilities = [],
        array $compoundAbilities = [],
    ) {
        foreach ($baseAbilities as $ability) {
            $this->base[$ability->name] = $ability;
        }

        foreach ($compoundAbilities as $ability) {
            $this->compound[$ability->name] = $ability;
        }
    }

    public function get(string $name): ?AbilityInterface
    {
        return $this->base[$name] ?? $this->compound[$name] ?? null;
    }

    /**
     * @return AbilitiesData
     */
    public function jsonSerialize(): array
    {
        return [
            'base'     => \array_map(fn(AbilityInterface $ability) => $ability->jsonSerialize(), $this->base),
            'compound' => \array_map(fn(AbilityInterface $ability) => $ability->jsonSerialize(), $this->compound),
        ];
    }
}
