<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Rpg\Damages;

use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Exception\Rpg\DamageException;
use Velkuns\GameTextEngine\Rpg\Damages\DamagesDetail;
use Velkuns\GameTextEngine\Rpg\Damages\DamagesDetailInterface;
use Velkuns\GameTextEngine\Rpg\Damages\Damages;

class DamagesTest extends TestCase
{
    public function testGet(): void
    {
        $damages = ['physical' => $this->getPhysicalDamage(), 'fire' => $this->getFireDamage()];

        self::assertSame($damages['physical'], (new Damages($damages))->get('physical'));
    }

    public function testGetButDamageTypeDoesNotExist(): void
    {
        $damages = ['physical' => $this->getPhysicalDamage(), 'fire' => $this->getFireDamage()];

        self::expectException(DamageException::class);
        self::expectExceptionCode(1350);
        (new Damages($damages))->get('unknown');
    }

    public function testGetAll(): void
    {
        $damages = ['physical' => $this->getPhysicalDamage(), 'fire' => $this->getFireDamage()];

        self::assertSame($damages, (new Damages($damages))->getAll());
    }

    public function testJsonSerialize(): void
    {
        $expected = [
            'physical' => ['type' => 'physical', 'value' => 2],
            'fire'     => ['type' => 'fire', 'value' => 1],
        ];

        $damages = ['physical' => $this->getPhysicalDamage(), 'fire' => $this->getFireDamage()];

        self::assertSame($expected, (new Damages($damages))->jsonSerialize());
    }

    public function testClone(): void
    {
        $damages = ['physical' => $this->getPhysicalDamage(), 'fire' => $this->getFireDamage()];
        $entityDamages = new Damages($damages);

        self::assertNotSame($entityDamages, $entityDamages->clone());
        self::assertEquals($entityDamages, $entityDamages->clone());
    }

    private function getPhysicalDamage(): DamagesDetailInterface
    {
        return new DamagesDetail('physical', 2);
    }

    private function getFireDamage(): DamagesDetailInterface
    {
        return new DamagesDetail('fire', 1);
    }
}
