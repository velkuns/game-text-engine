<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Alteration;

/**
 * @phpstan-type AlterationDurationData array{
 *  max: int,
 *  remaining?: int,
 * }
 */
class AlterationDuration
{
    public function __construct(
        private readonly int $max = 0,
        private int $remaining = 0,
    ) {}

    public function getMax(): int
    {
        return $this->max;
    }

    public function getRemaining(): int
    {
        return $this->remaining;
    }

    public function decrease(): self
    {
        $this->remaining = \max($this->remaining - 1, 0);

        return $this;
    }

    /**
     * @return AlterationDurationData
     */
    public function jsonSerialize(): array
    {
        $data = ['max' => $this->max];

        if ($this->max > $this->remaining) {
            $data['remaining'] = $this->remaining;
        }

        return $data;
    }

    public function clone(): self
    {
        return new self(
            $this->max,
            $this->remaining,
        );
    }
}
