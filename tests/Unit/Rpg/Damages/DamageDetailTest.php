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
use Velkuns\GameTextEngine\Rpg\Damages\DamagesDetail;
use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

class DamageDetailTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $expected = [
            'type'  => 'physical',
            'value' => 2,
        ];

        self::assertEquals($expected, (new DamagesDetail('physical', 2))->jsonSerialize());
    }

    public function testClone(): void
    {
        $expected = new DamagesDetail('physical', 2);

        self::assertNotSame($expected, $expected->clone());
        self::assertEquals($expected, $expected->clone());
    }

    public function testGetType(): void
    {
        $expected = 'physical';

        self::assertSame($expected, (new DamagesDetail('physical', 2))->getType());
    }

    public function testGetValue(): void
    {
        $expected = 2;

        self::assertSame($expected, (new DamagesDetail('physical', 2))->getValue());
    }

    public function testIncrease(): void
    {
        $expected = 4;

        self::assertSame($expected, (new DamagesDetail('physical', 2))->increase(2)->getValue());
    }

    public function testDecrease(): void
    {
        $expected = 2;

        self::assertSame($expected, (new DamagesDetail('physical', 4))->decrease(2)->getValue());
    }

    public function testGetValueWithModifiers(): void
    {
        $expected = 4;

        $modifiers = [
            new Modifier('self.damages.physical.value', 2),
            new Modifier('self.damages.fire.value', 3),
        ];

        self::assertSame($expected, (new DamagesDetail('physical', 2))->getValueWithModifiers($modifiers));
    }
}
