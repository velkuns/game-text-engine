<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Factory;

use Velkuns\GameTextEngine\Element\Damage\Damage;
use Velkuns\GameTextEngine\Element\Damage\Damages;

/**
 * @phpstan-import-type DamageData from Damage
 * @phpstan-import-type DamagesData from Damages
 */
readonly class DamageFactory
{
    /**
     * @phpstan-param DamagesData $data
     */
    public function fromDamages(array $data): Damages
    {
        return new Damages(\array_map($this->fromDamage(...), $data));
    }

    /**
     * @phpstan-param DamageData $data
     */
    public function fromDamage(array $data): Damage
    {
        return new Damage($data['type'], $data['value']);
    }
}
