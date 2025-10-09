<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Modifier;

/**
 * @phpstan-type ModifierData array{
 *     name: string,
 *     description: string,
 *     targetAbility: string,
 *     value: int,
 * }
 */
readonly class Modifier implements \JsonSerializable
{
    public function __construct(
        public string $name,
        public string $targetAbility,
        public int $value,
        public string $description = '',
    ) {}

    /**
     * @return ModifierData
     */
    public function jsonSerialize(): array
    {
        return [
            'name'          => $this->name,
            'targetAbility' => $this->targetAbility,
            'value'         => $this->value,
            'description'   => $this->description,
        ];
    }
}
