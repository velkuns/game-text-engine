<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Factory;

use Velkuns\GameTextEngine\Rpg\Alteration\Alteration;
use Velkuns\GameTextEngine\Rpg\Alteration\AlterationDuration;
use Velkuns\GameTextEngine\Rpg\Alteration\AlterationInterface;

/**
 * @phpstan-import-type AlterationData from AlterationInterface
 */
readonly class AlterationFactory
{
    public function __construct(
        private ModifierFactory $modifierFactory,
    ) {}

    /**
     * @phpstan-param AlterationData $data
     */
    public function from(array $data): AlterationInterface
    {
        $type           = $data['type'];
        $name           = $data['name'];
        $description    = $data['description'];
        $modifiers      = \array_map(fn($modifier) => $this->modifierFactory->from($modifier), $data['modifiers']);

        $max       = $data['duration']['max'];
        $remaining = $data['duration']['remaining'] ?? $max;

        return new Alteration($type, $name, $description, $modifiers, new AlterationDuration($max, $remaining));
    }
}
