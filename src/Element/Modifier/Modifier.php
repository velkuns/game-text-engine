<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Modifier;

/**
 * @phpstan-type ModifierData array{
 *     ability: string,
 *     value: int,
 * }
 */
readonly class Modifier implements \JsonSerializable
{
    public function __construct(
        public string $ability,
        public int $value,
    ) {}

    /**
     * @return ModifierData
     */
    public function jsonSerialize(): array
    {
        return [
            'ability' => $this->ability,
            'value'   => $this->value,
        ];
    }
}
