<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Element\Damage;

use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Element\Damage\Damage;
use Velkuns\GameTextEngine\Element\Damage\DamageInterface;
use Velkuns\GameTextEngine\Element\Damage\Damages;
use Velkuns\GameTextEngine\Exception\Element\DamageException;

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

    private function getPhysicalDamage(): DamageInterface
    {
        return new Damage('physical', 2);
    }

    private function getFireDamage(): DamageInterface
    {
        return new Damage('fire', 1);
    }
}
