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
 * @phpstan-type EquipmentItemData array{name: string, probability: float}
 * @phpstan-type EquipmentData array{
 *     weapon?: list<EquipmentItemData>,
 * }
 */
readonly class EntityEquipment implements \JsonSerializable
{
    /**
     * @param EquipmentData $equipment
     */
    public function __construct(
        public array $equipment = [],
    ) {}

    /**
     * @return EquipmentData
     */
    public function jsonSerialize(): array
    {
        return $this->equipment;
    }

    public function clone(): self
    {
        return new self(
            $this->equipment,
        );
    }
}
