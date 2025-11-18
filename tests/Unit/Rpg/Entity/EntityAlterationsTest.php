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
use Velkuns\GameTextEngine\Exception\Rpg\AlterationException;
use Velkuns\GameTextEngine\Rpg\Alteration\AlterationDuration;
use Velkuns\GameTextEngine\Rpg\Entity\EntityAlterations;
use Velkuns\GameTextEngine\Rpg\Alteration\Alteration;

class EntityAlterationsTest extends TestCase
{
    public function testSetButTypeDoesNotExist(): void
    {
        $alterations = new EntityAlterations(['test' => []]);

        $alterations->set(new Alteration('test', 'New test alteration', '', [], new AlterationDuration()));

        self::expectException(AlterationException::class);
        self::expectExceptionCode(1540);
        $alterations->set(new Alteration('unknown', 'New alteration', '', [], new AlterationDuration()));
    }
}
