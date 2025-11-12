<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Rpg\Entity;

use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Exception\Rpg\TraitException;
use Velkuns\GameTextEngine\Rpg\Entity\EntityTraits;
use Velkuns\GameTextEngine\Rpg\Traits\EntityTrait;

class EntityTraitsTest extends TestCase
{
    public function testSetButTypeDoesNotExist(): void
    {
        $traits = new EntityTraits(['test' => []]);

        $traits->set(new EntityTrait('test', 'New test trait', '', []));

        self::expectException(TraitException::class);
        self::expectExceptionCode(1500);
        $traits->set(new EntityTrait('unknown', 'New Trait', '', []));
    }
}
