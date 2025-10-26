<?php

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Element\Modifier;

use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Element\Exception\ModifierException;
use Velkuns\GameTextEngine\Element\Exception\TypeElementResolveException;
use Velkuns\GameTextEngine\Element\Modifier\Modifier;
use Velkuns\GameTextEngine\Element\Modifier\ModifierProcessor;
use Velkuns\GameTextEngine\Element\Resolver\TypeElementResolver;
use Velkuns\GameTextEngine\Tests\Helper\ApiTrait;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;

class ModifierProcessorTest extends TestCase
{
    use ApiTrait;
    use EntityTrait;

    public function testApply(): void
    {
        $player    = self::getPlayer();
        $processor = new ModifierProcessor(new TypeElementResolver());
        $modifier  = new Modifier('self.abilities.strength.value', 5);

        //~ Check initial value
        self::assertSame(10, $player->getAbilities()->get('strength')?->getValue());

        $processor->apply($modifier, $player);

        self::assertSame(15, $player->getAbilities()->get('strength')?->getValue());
    }

    public function testApplyButPropertyDoesNotExists(): void
    {
        $player    = self::getPlayer();
        $processor = new ModifierProcessor(new TypeElementResolver());
        $modifier  = new Modifier('self.abilities.strength.values', 5);

        self::expectException(ModifierException::class);
        self::expectExceptionCode(1201);
        $processor->apply($modifier, $player);
    }

    public function testApplyOnEnemyButEnemyIsNull(): void
    {
        $player    = self::getPlayer();
        $processor = new ModifierProcessor(new TypeElementResolver());
        $modifier  = new Modifier('enemy.abilities.strength.values', -5);

        self::expectException(ModifierException::class);
        self::expectExceptionCode(1200);
        $processor->apply($modifier, $player);
    }

    public function testApplyButTypeRefersToList(): void
    {
        $player    = self::getPlayer();
        $player->getInventory()->add(self::getItems()->get('Small Health Potion'));

        $processor = new ModifierProcessor(new TypeElementResolver());
        $modifier  = new Modifier('self.inventory.items.name', -5);

        self::expectException(TypeElementResolveException::class);
        self::expectExceptionCode(1110);
        $processor->apply($modifier, $player);
    }
}
