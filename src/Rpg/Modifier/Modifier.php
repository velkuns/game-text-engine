<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Modifier;

use Velkuns\GameTextEngine\Core\Condition\Conditions;

/**
 * @phpstan-import-type ConditionsData from Conditions
 * @phpstan-type ModifierData array{
 *     type: string,
 *     value: int,
 *     conditions?: ConditionsData,
 * }
 */
readonly class Modifier implements \JsonSerializable
{
    public function __construct(
        public string $type,
        public int $value,
        public ?Conditions $conditions = null,
    ) {}

    /**
     * @return ModifierData
     */
    public function jsonSerialize(): array
    {
        $data = [
            'type'  => $this->type,
            'value' => $this->value,
        ];

        if ($this->conditions !== null) {
            $data['conditions'] = $this->conditions->jsonSerialize();
        }

        return $data;
    }

    public function clone(): self
    {
        return new self(
            type: $this->type,
            value: $this->value,
            conditions: $this->conditions?->clone(),
        );
    }
}
