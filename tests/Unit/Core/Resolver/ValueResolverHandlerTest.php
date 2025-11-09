<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Core\Resolver;

use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Exception\Core\UnsupportedTypeElementResolverException;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;
use Velkuns\GameTextEngine\Tests\Helper\ResolverTrait;

class ValueResolverHandlerTest extends TestCase
{
    use EntityTrait;
    use ResolverTrait;

    public function testHandleOnAbility(): void
    {
        $resolver = self::getValueResolverHandler();
        $player   = self::getPlayer();

        $value = $resolver->handle('self.ability.strength.value', $player);

        self::assertSame(10, $value);
    }

    public function testHandleOnDamages(): void
    {
        $resolver = self::getValueResolverHandler();
        $player   = self::getPlayer();

        $value = $resolver->handle('self.damages.physical.value', $player);

        self::assertSame(0, $value);
    }

    public function testHandleOnDamagesWithModifiers(): void
    {
        $resolver = self::getValueResolverHandler();
        $player   = self::getPlayer();
        $enemy    = self::getGoblin();

        $modifiers = $player->getModifiers($enemy);

        $value = $resolver->handle('self.damages.physical.value_with_modifiers', $player, $modifiers);

        self::assertSame(1, $value);
    }

    public function testHandleOnInfoLevel(): void
    {
        $resolver = self::getValueResolverHandler();
        $player   = self::getPlayer();

        $value = $resolver->handle('self.info.level', $player);

        self::assertSame(5, $value);
    }

    public function testHandleOnEquippedWeapon(): void
    {
        $resolver = self::getValueResolverHandler();
        $player   = self::getPlayer();

        $value = $resolver->handle('attacker.weapon.equipped.damages', $player);

        self::assertSame(2, $value);
    }

    public function testHandleOnUnequippedWeapon(): void
    {
        $resolver = self::getValueResolverHandler();
        $player   = self::getPlayer();
        $player->getInventory()->getEquippedWeapon()?->unequip();

        $value = $resolver->handle('attacker.weapon.equipped.damages', $player);

        self::assertSame(0, $value);
    }

    public function testHandleOnRoll(): void
    {
        $resolver = self::getValueResolverHandler();
        $player   = self::getPlayer();

        $value = $resolver->handle('attacker.roll(100)', $player);

        self::assertSame(38, $value);
    }

    public function testHandleOnInventoryItems(): void
    {
        $resolver = self::getValueResolverHandler();
        $player   = self::getPlayer();

        self::expectException(UnsupportedTypeElementResolverException::class);

        $resolver->handle('self.inventory.item', $player);
    }
}
