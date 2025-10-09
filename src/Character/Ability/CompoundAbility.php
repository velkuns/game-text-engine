<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Character\Ability;

/**
 * @phpstan-type CompoundAbilityData array{
 *       type: 'compound',
 *       rule: string,
 *     }
 */
readonly class CompoundAbility implements AbilityInterface, \JsonSerializable
{
    /**
     * @param array<string, BaseAbility> $abilities
     */
    public function __construct(
        public string $name,
        public string $rule,
        public array $abilities,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * /!\ Currently supports only sum of base abilities.
     *
     *
     * @todo Implement other rules like multiply, subtract, mixed operations, etc.s
     */
    public function getValue(): int
    {
        return \array_sum(\array_map(fn(BaseAbility $ability) => $ability->getValue(), $this->abilities));
    }

    public function getInitialValue(): int
    {
        return \array_sum(\array_map(fn(BaseAbility $ability) => $ability->getInitialValue(), $this->abilities));
    }

    public function getRangeMin(): int
    {
        return \array_sum(\array_map(fn(BaseAbility $ability) => $ability->getRangeMin(), $this->abilities));
    }

    public function getRangeMax(): int
    {
        return \array_sum(\array_map(fn(BaseAbility $ability) => $ability->getRangeMax(), $this->abilities));
    }

    /**
     * @return non-empty-array<string, CompoundAbilityData>
     */
    public function jsonSerialize(): array
    {
        return [
            $this->name => [
                'type'   => 'compound',
                'rule'  => $this->rule,
            ],
        ];
    }
}
