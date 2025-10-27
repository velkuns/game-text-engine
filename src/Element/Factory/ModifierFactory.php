<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Factory;

use Velkuns\GameTextEngine\Element\Modifier\Modifier;

/**
 * @phpstan-import-type ModifierData from Modifier
 */
readonly class ModifierFactory
{
    /**
     * @phpstan-param ModifierData $data
     */
    public function from(array $data): Modifier
    {
        $type  = $data['type'];
        $value = $data['value'];

        return new Modifier($type, $value);
    }
}
