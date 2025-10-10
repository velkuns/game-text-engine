<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Ability;

/**
 * @phpstan-import-type ConstraintsAbilityData from ConstraintsAbility
 * @phpstan-type BaseAbilityData array{
 *     type: 'base',
 *     name: string,
 *     initial: int,
 *     current: int,
 *     max: int,
 *     constraints: ConstraintsAbilityData,
 *     rule: string|null,
 * }
 */
readonly class BaseAbility implements AbilityInterface
{
    public function __construct(
        public string $name,
        public int $current,
        public int $max,
        public ConstraintsAbility $constraints = new ConstraintsAbility(0, 12),
        public int $initial = 0,
        public ?string $rule = null,
    ) {}

    public function getType(): AbilityType
    {
        return AbilityType::Base;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCurrent(): int
    {
        return $this->current;
    }

    public function getInitial(): int
    {
        return $this->initial;
    }

    public function getConstraints(): ConstraintsAbility
    {
        return $this->constraints;
    }

    /**
     * @return BaseAbilityData
     */
    public function jsonSerialize(): array
    {
        return [
            'type'        => AbilityType::Base->value,
            'name'        => $this->name,
            'current'     => $this->current,
            'max'         => $this->max,
            'constraints' => $this->constraints->jsonSerialize(),
            'initial'     => $this->initial,
            'rule'        => $this->rule,
        ];
    }
}
