<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Factory;

use Velkuns\GameTextEngine\Element\Status\Status;
use Velkuns\GameTextEngine\Element\Status\StatusInterface;

/**
 * @phpstan-import-type StatusData from StatusInterface
 */
readonly class StatusFactory
{
    public function __construct(
        private ModifierFactory $modifierFactory,
        private ConditionsFactory $conditionsFactory,
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
        $conditions     = $this->conditionsFactory->from($data['conditions'] ?? null);
        $durationTurns  = $data['durationTurns'] ?? 0;
        $remainingTurns = $data['remainingTurns'] ?? 0;

        return new Status($type, $name, $description, $modifiers, $conditions, $durationTurns, $remainingTurns);
    }
}
