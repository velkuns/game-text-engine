<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Entity;

/**
 * @phpstan-type EntityInfoData array{
 *     level: int,
 *     xp: int,
 *     age: int,
 *     size: string,
 *     race: string,
 *     gender: string,
 *     description: string,
 *     background: string,
 *     notes: string,
 * }
 */
class EntityInfo implements \JsonSerializable
{
    public function __construct(
        public int $level,
        public int $xp,
        public int $age,
        public string $size,
        public string $race,
        public string $gender,
        public string $description,
        public string $background,
        public string $notes,
    ) {}

    /**
     * @return EntityInfoData
     */
    public function jsonSerialize(): array
    {
        return [
            'level'       => $this->level,
            'xp'          => $this->xp,
            'age'         => $this->age,
            'size'        => $this->size,
            'race'        => $this->race,
            'gender'      => $this->gender,
            'description' => $this->description,
            'background'  => $this->background,
            'notes'       => $this->notes,
        ];
    }

    public function clone(): self
    {
        return new self(
            $this->level,
            $this->xp,
            $this->age,
            $this->size,
            $this->race,
            $this->gender,
            $this->description,
            $this->background,
            $this->notes,
        );
    }
}
