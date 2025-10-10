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
 * @phpstan-type BaseAbilityData array{
 *       type: 'base',
 *       value: int,
 *       initial: int,
 *       range: array{min: int, max: int}
 *     }
 */
readonly class BaseAbility implements AbilityInterface, \JsonSerializable
{
    /**
     * @param array{min: int, max: int} $range
     */
    public function __construct(
        public string $name,
        public int $value = 0,
        public int $initial = 0,
        public array $range = ['min' => 0, 'max' => 12],
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getInitialValue(): int
    {
        return $this->initial;
    }

    public function getRangeMin(): int
    {
        return $this->range['min'];
    }

    public function getRangeMax(): int
    {
        return $this->range['max'];
    }

    /**
     * @return non-empty-array<string, BaseAbilityData>
     */
    public function jsonSerialize(): array
    {
        return [
            $this->name => [
                'type'    => 'base',
                'value'   => $this->value,
                'initial' => $this->initial,
                'range'   => $this->range,
            ],
        ];
    }
}
