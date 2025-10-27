<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Element\Damage;

use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Element\Damage\Damage;
use Velkuns\GameTextEngine\Element\Modifier\Modifier;

class DamageTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $expected = [
            'type'  => 'physical',
            'value' => 2,
        ];

        self::assertEquals($expected, (new Damage('physical', 2))->jsonSerialize());
    }

    public function testClone(): void
    {
        $expected = new Damage('physical', 2);

        self::assertNotSame($expected, $expected->clone());
        self::assertEquals($expected, $expected->clone());
    }

    public function testGetType(): void
    {
        $expected = 'physical';

        self::assertSame($expected, (new Damage('physical', 2))->getType());
    }

    public function testGetValue(): void
    {
        $expected = 2;

        self::assertSame($expected, (new Damage('physical', 2))->getValue());
    }

    public function testGetValueWithModifiers(): void
    {
        $expected = 4;

        $modifiers = [
            new Modifier('self.damages.physical.value', 2),
            new Modifier('self.damages.fire.value', 3),
        ];

        self::assertSame($expected, (new Damage('physical', 2))->getValueWithModifiers($modifiers));
    }
}
