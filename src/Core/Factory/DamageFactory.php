<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Factory;

use Velkuns\GameTextEngine\Rpg\Damages\DamagesDetail;
use Velkuns\GameTextEngine\Rpg\Damages\Damages;

/**
 * @phpstan-import-type DamageData from DamagesDetail
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
    public function fromDamage(array $data): DamagesDetail
    {
        return new DamagesDetail($data['type'], $data['value']);
    }
}
