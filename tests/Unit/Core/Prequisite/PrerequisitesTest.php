<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Core\Prequisite;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Core\Prerequisite\Prerequisites;
use Velkuns\GameTextEngine\Exception\Core\ResolverException;
use Velkuns\GameTextEngine\Exception\Core\UnsupportedConditionTypeException;
use Velkuns\GameTextEngine\Exception\Core\UnsupportedTypeElementResolverException;
use Velkuns\GameTextEngine\Exception\Rpg\TraitException;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;

class PrerequisitesTest extends TestCase
{
    use EntityTrait;
    use FactoryTrait;

    #[DataProvider('evaluateDataProvider')]
    public function testEvaluate(
        ?Prerequisites $prerequisites,
        EntityInterface $player,
        ?EntityInterface $enemy,
        bool $evaluation,
    ): void {
        self::assertSame($evaluation, $prerequisites?->evaluate($player, $enemy));
    }

    /**
     * @phpstan-param class-string<\Throwable> $exceptionClass
     */
    #[DataProvider('evaluateExceptionDataProvider')]
    public function testEvaluateThatThrowException(
        ?Prerequisites $prerequisites,
        EntityInterface $player,
        EntityInterface $enemy,
        string $exceptionClass,
    ): void {
        self::expectException($exceptionClass);
        $prerequisites?->evaluate($player, $enemy);
    }

    /**
     * @return array<string, array{0: Prerequisites|null, 1: EntityInterface, 2: EntityInterface|null, 3: bool}>
     */
    public static function evaluateDataProvider(): array
    {
        self::setUpBeforeClass();

        return [
            'evaluate required 1 condition with list of 1 conditions' => [
                self::getPrerequisitesFactory()->from([
                    'numberRequired' => 1,
                    'requirements'   => [
                        [
                            'type'      => 'self.attribute.strength',
                            'condition' => 'value >=  10 ',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                true,
            ],
            'evaluate required conditions based on entity info level' => [
                self::getPrerequisitesFactory()->from([
                    'numberRequired' => 1,
                    'requirements'   => [
                        [
                            'type'      => 'self.info',
                            'condition' => 'level>=0',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                true,
            ],
            'evaluate required conditions based on entity info size' => [
                self::getPrerequisitesFactory()->from([
                    'numberRequired' => 1,
                    'requirements'   => [
                        [
                            'type'      => 'self.info',
                            'condition' => 'size!=huge',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                true,
            ],
            'evaluate required conditions based on entity trait but not found' => [
                self::getPrerequisitesFactory()->from([
                    'numberRequired' => 1,
                    'requirements'   => [
                        [
                            'type'      => 'self.trait.skill',
                            'condition' => 'name=unknown',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                false,
            ],
            'evaluate required 1 condition with list of 1 conditions but evaluation failed' => [
                self::getPrerequisitesFactory()->from([
                    'numberRequired' => 1,
                    'requirements'   => [
                        [
                            'type'      => 'self.trait.skill',
                            'condition' => 'name=Sword (Mastery)',
                            'is'        => false,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                false,
            ],
            'evaluate required 1 condition with list of 2 conditions' => [
                self::getPrerequisitesFactory()->from([
                    'numberRequired' => 1,
                    'requirements'   => [
                        [
                            'type'      => 'self.attribute.strength',
                            'condition' => 'value>10',
                            'is'        => true,
                        ],
                        [
                            'type'      => 'self.attribute.agility',
                            'condition' => 'value=15',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                true,
            ],
            'evaluate required 2 condition with list of 2 conditions' => [
                self::getPrerequisitesFactory()->from([
                    'numberRequired' => 2,
                    'requirements'   => [
                        [
                            'type'      => 'self.attribute.strength',
                            'condition' => 'value<=10',
                            'is'        => true,
                        ],
                        [
                            'type'      => 'self.attribute.agility',
                            'condition' => 'value>0',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                true,
            ],
            'evaluate required 1 condition with list of 1 conditions on specific item' => [
                self::getPrerequisitesFactory()->from([
                    'numberRequired' => 1,
                    'requirements'   => [
                        [
                            'type'      => 'self.inventory.item',
                            'condition' => 'name=The Sword;subType=sword;equipped=true;flags&4',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                true,
            ],
            'evaluate required 1 condition with list of 1 conditions on specific item but evaluation failed' => [
                self::getPrerequisitesFactory()->from([
                    'numberRequired' => 1,
                    'requirements'   => [
                        [
                            'type'      => 'self.inventory.item',
                            'condition' => 'name=The Sword;subType=sword;equipped=false;flags&4',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                false,
            ],
            'evaluate required conditions based on enemy info but no enemy entity given ' => [
                self::getPrerequisitesFactory()->from([
                    'numberRequired' => 1,
                    'requirements'   => [
                        [
                            'type'      => 'enemy.info',
                            'condition' => 'race=goblin',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                null,
                false,
            ],
        ];
    }

    /**
     * @return array<string, array{0: Prerequisites|null, 1: EntityInterface, 2: EntityInterface, 3: class-string<\Throwable>}>
     */
    public static function evaluateExceptionDataProvider(): array
    {
        self::setUpBeforeClass();

        return [
            'evaluate with not supported type property' => [
                self::getPrerequisitesFactory()->from([
                    'numberRequired' => 1,
                    'requirements'   => [
                        [
                            'type'      => 'self.unknown.property',
                            'condition' => 'value >=  10 ',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                UnsupportedTypeElementResolverException::class,
            ],
            'evaluate required conditions based on trait but trait type does not exist' => [
                self::getPrerequisitesFactory()->from([
                    'numberRequired' => 1,
                    'requirements'   => [
                        [
                            'type'      => 'self.trait.unknown',
                            'condition' => 'name=test',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                TraitException::class,
            ],
            'evaluate required conditions based on element that is not supported' => [
                self::getPrerequisitesFactory()->from([
                    'numberRequired' => 1,
                    'requirements'   => [
                        [
                            'type'      => 'self.damages.physical', // currently haven't damages condition validator
                            'condition' => 'value>2',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                UnsupportedConditionTypeException::class,
            ],
            'evaluate required conditions based on attribute but property not supported' => [
                self::getPrerequisitesFactory()->from([
                    'numberRequired' => 1,
                    'requirements'   => [
                        [
                            'type'      => 'self.damages.physical', // currently haven't damages condition validator
                            'condition' => 'value>2',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                UnsupportedConditionTypeException::class,
            ],
            'evaluate required conditions based on attribute but attribute not found' => [
                self::getPrerequisitesFactory()->from([
                    'numberRequired' => 1,
                    'requirements'   => [
                        [
                            'type'      => 'self.attribute.unknown',
                            'condition' => 'value>2',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                ResolverException::class,
            ],
            'evaluate required conditions based on damages type but type not found' => [
                self::getPrerequisitesFactory()->from([
                    'numberRequired' => 1,
                    'requirements'   => [
                        [
                            'type'      => 'self.damages.unknown',
                            'condition' => 'value>2',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                ResolverException::class,
            ],
        ];
    }
}
