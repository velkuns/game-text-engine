<?php

declare(strict_types=1);

namespace Rpg\Modifier;

use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Exception\Core\UnsupportedModifierProcessorPropertyException;
use Velkuns\GameTextEngine\Exception\Rpg\ModifierException;
use Velkuns\GameTextEngine\Rpg\Modifier\AttributeModifierProcessor;
use Velkuns\GameTextEngine\Rpg\Modifier\DamagesModifierProcessor;
use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;
use Velkuns\GameTextEngine\Rpg\Modifier\ModifierHandler;
use Velkuns\GameTextEngine\Tests\Helper\ApiTrait;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;
use Velkuns\GameTextEngine\Tests\Helper\ResolverTrait;

class ModifierHandlerTest extends TestCase
{
    use ApiTrait;
    use EntityTrait;
    use ResolverTrait;

    public function testApplyOnAttribute(): void
    {
        $player   = self::getPlayer();
        $handler  = new ModifierHandler(self::getTypeResolverHandler(), [new AttributeModifierProcessor(), new DamagesModifierProcessor()]);
        $modifier = new Modifier('self.attribute.strength.value', 5);

        //~ Check initial value
        self::assertSame(10, $player->getAttributes()->get('strength')?->getValue());

        $handler->handle($modifier, $player);

        self::assertSame(15, $player->getAttributes()->get('strength')?->getValue());
    }

    public function testApplyOnAttributeButPropertyDoesNotExists(): void
    {
        $player   = self::getPlayer();
        $handler  = new ModifierHandler(self::getTypeResolverHandler(), [new AttributeModifierProcessor(), new DamagesModifierProcessor()]);
        $modifier = new Modifier('self.attribute.strength.values', 5);

        self::expectException(UnsupportedModifierProcessorPropertyException::class);
        $handler->handle($modifier, $player);
    }

    public function testApplyOnDamages(): void
    {
        $player   = self::getPlayer();
        $handler  = new ModifierHandler(self::getTypeResolverHandler(), [new AttributeModifierProcessor(), new DamagesModifierProcessor()]);
        $modifier = new Modifier('self.damages.physical.value', 2);

        //~ Check initial value
        self::assertSame(0, $player->getDamages()->get('physical')->getValue());

        $handler->handle($modifier, $player);

        self::assertSame(2, $player->getDamages()->get('physical')->getValue());
    }

    public function testApplyOnDamagesButPropertyDoesNotExists(): void
    {
        $player   = self::getPlayer();
        $handler  = new ModifierHandler(self::getTypeResolverHandler(), [new AttributeModifierProcessor(), new DamagesModifierProcessor()]);
        $modifier = new Modifier('self.damages.physical.values', 5);

        self::expectException(UnsupportedModifierProcessorPropertyException::class);
        $handler->handle($modifier, $player);
    }

    public function testApplyOnEnemyButEnemyIsNull(): void
    {
        $player   = self::getPlayer();
        $handler  = new ModifierHandler(self::getTypeResolverHandler(), [new AttributeModifierProcessor(), new DamagesModifierProcessor()]);
        $modifier = new Modifier('enemy.attribute.strength.values', -5);

        self::expectException(ModifierException::class);
        self::expectExceptionCode(1200);
        $handler->handle($modifier, $player);
    }
}
