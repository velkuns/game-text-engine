<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Element\Entity;

use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Element\Entity\EntityStatuses;
use Velkuns\GameTextEngine\Element\Exception\StatusException;
use Velkuns\GameTextEngine\Element\Status\Status;

class EntityStatusesTest extends TestCase
{
    public function testSetButTypeDoesNotExist(): void
    {
        $statuses = new EntityStatuses(['test' => []]);

        $statuses->set(new Status('test', 'New test status', '', []));

        self::expectException(StatusException::class);
        self::expectExceptionCode(1500);
        $statuses->set(new Status('unknown', 'New Status', '', []));
    }
}
