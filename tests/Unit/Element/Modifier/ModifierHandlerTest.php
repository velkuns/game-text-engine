<?php

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Element\Modifier;

use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Element\Modifier\AbilityModifierProcessor;
use Velkuns\GameTextEngine\Element\Modifier\DamagesModifierProcessor;
use Velkuns\GameTextEngine\Element\Modifier\Modifier;
use Velkuns\GameTextEngine\Element\Modifier\ModifierHandler;
use Velkuns\GameTextEngine\Exception\Element\ModifierException;
use Velkuns\GameTextEngine\Exception\Element\UnsupportedModifierProcessorPropertyException;
use Velkuns\GameTextEngine\Tests\Helper\ApiTrait;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;
use Velkuns\GameTextEngine\Tests\Helper\ResolverTrait;

class ModifierHandlerTest extends TestCase
{
    use ApiTrait;
    use EntityTrait;
    use ResolverTrait;

    public function testApplyOnAbility(): void
    {
        $player   = self::getPlayer();
        $handler  = new ModifierHandler(self::getResolverHandler(), [new AbilityModifierProcessor(), new DamagesModifierProcessor()]);
        $modifier = new Modifier('self.ability.strength.value', 5);

        //~ Check initial value
        self::assertSame(10, $player->getAbilities()->get('strength')?->getValue());

        $handler->handle($modifier, $player);

        self::assertSame(15, $player->getAbilities()->get('strength')?->getValue());
    }

    public function testApplyOnAbilityButPropertyDoesNotExists(): void
    {
        $player   = self::getPlayer();
        $handler  = new ModifierHandler(self::getResolverHandler(), [new AbilityModifierProcessor(), new DamagesModifierProcessor()]);
        $modifier = new Modifier('self.ability.strength.values', 5);

        self::expectException(UnsupportedModifierProcessorPropertyException::class);
        $handler->handle($modifier, $player);
    }

    public function testApplyOnDamages(): void
    {
        $player   = self::getPlayer();
        $handler  = new ModifierHandler(self::getResolverHandler(), [new AbilityModifierProcessor(), new DamagesModifierProcessor()]);
        $modifier = new Modifier('self.damages.physical.value', 2);

        //~ Check initial value
        self::assertSame(0, $player->getDamages()->get('physical')->getValue());

        $handler->handle($modifier, $player);

        self::assertSame(2, $player->getDamages()->get('physical')->getValue());
    }

    public function testApplyOnDamagesButPropertyDoesNotExists(): void
    {
        $player   = self::getPlayer();
        $handler  = new ModifierHandler(self::getResolverHandler(), [new AbilityModifierProcessor(), new DamagesModifierProcessor()]);
        $modifier = new Modifier('self.damages.physical.values', 5);

        self::expectException(UnsupportedModifierProcessorPropertyException::class);
        $handler->handle($modifier, $player);
    }

    public function testApplyOnEnemyButEnemyIsNull(): void
    {
        $player   = self::getPlayer();
        $handler  = new ModifierHandler(self::getResolverHandler(), [new AbilityModifierProcessor(), new DamagesModifierProcessor()]);
        $modifier = new Modifier('enemy.ability.strength.values', -5);

        self::expectException(ModifierException::class);
        self::expectExceptionCode(1200);
        $handler->handle($modifier, $player);
    }
}
