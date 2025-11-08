<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Factory;

use Velkuns\GameTextEngine\Rpg\Status\Status;
use Velkuns\GameTextEngine\Rpg\Status\StatusInterface;

/**
 * @phpstan-import-type StatusData from StatusInterface
 */
readonly class StatusFactory
{
    public function __construct(
        private ModifierFactory $modifierFactory,
    ) {}

    /**
     * @param StatusData $data
     */
    public function from(array $data): Status
    {
        $type           = $data['type'];
        $name           = $data['name'];
        $description    = $data['description'];
        $modifiers      = \array_map(fn($modifier) => $this->modifierFactory->from($modifier), $data['modifiers']);
        $durationTurns  = $data['durationTurns'] ?? 0;
        $remainingTurns = $data['remainingTurns'] ?? 0;

        return new Status($type, $name, $description, $modifiers, $durationTurns, $remainingTurns);
    }
}
