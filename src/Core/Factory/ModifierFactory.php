<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Factory;

use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

/**
 * @phpstan-import-type ModifierData from Modifier
 */
readonly class ModifierFactory
{
    public function __construct(
        private PrerequisitesFactory $prerequisitesFactory,
    ) {}

    /**
     * @phpstan-param ModifierData $data
     */
    public function from(array $data): Modifier
    {
        $type          = $data['type'];
        $value         = $data['value'];
        $prerequisites = $this->prerequisitesFactory->from($data['prerequisites'] ?? null);

        return new Modifier($type, $value, $prerequisites);
    }
}
