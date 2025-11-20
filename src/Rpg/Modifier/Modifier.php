<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Modifier;

use Velkuns\GameTextEngine\Core\Prerequisite\Prerequisites;

/**
 * @phpstan-import-type PrerequisitesData from Prerequisites
 * @phpstan-type ModifierData array{
 *     type: string,
 *     value: int,
 *     prerequisites?: PrerequisitesData,
 * }
 */
readonly class Modifier implements \JsonSerializable
{
    public function __construct(
        public string $type,
        public int $value,
        public ?Prerequisites $prerequisites = null,
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

        if ($this->prerequisites !== null) {
            $data['prerequisites'] = $this->prerequisites->jsonSerialize();
        }

        return $data;
    }

    public function clone(): self
    {
        return new self(
            $this->type,
            $this->value,
            $this->prerequisites?->clone(),
        );
    }
}
