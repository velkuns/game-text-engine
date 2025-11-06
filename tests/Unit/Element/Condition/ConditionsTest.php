<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Element\Condition;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Element\Condition\Conditions;
use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Exception\ResolverException;
use Velkuns\GameTextEngine\Element\Exception\StatusException;
use Velkuns\GameTextEngine\Element\Exception\UnsupportedConditionTypeException;
use Velkuns\GameTextEngine\Element\Exception\UnsupportedTypeElementResolverException;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;

class ConditionsTest extends TestCase
{
    use EntityTrait;
    use FactoryTrait;

    #[DataProvider('evaluateDataProvider')]
    public function testEvaluate(
        Conditions $conditions,
        EntityInterface $player,
        ?EntityInterface $enemy,
        bool $evaluation,
    ): void {
        self::assertSame($evaluation, $conditions->evaluate($player, $enemy));
    }

    /**
     * @phpstan-param class-string<\Throwable> $exceptionClass
     */
    #[DataProvider('evaluateExceptionDataProvider')]
    public function testEvaluateThatThrowException(
        Conditions $conditions,
        EntityInterface $player,
        EntityInterface $enemy,
        string $exceptionClass,
    ): void {
        self::expectException($exceptionClass);
        $conditions->evaluate($player, $enemy);
    }

    /**
     * @return array<string, array{0: Conditions|null, 1: EntityInterface, 2: EntityInterface|null, 3: bool}>
     */
    public static function evaluateDataProvider(): array
    {
        self::setUpBeforeClass();

        return [
            'evaluate required 1 condition with list of 1 conditions' => [
                self::getConditionFactory()->from([
                    'numberRequired' => 1,
                    'conditions' => [
                        [
                            'type'      => 'self.ability.strength',
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
                self::getConditionFactory()->from([
                    'numberRequired' => 1,
                    'conditions' => [
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
                self::getConditionFactory()->from([
                    'numberRequired' => 1,
                    'conditions' => [
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
            'evaluate required conditions based on entity status but not found' => [
                self::getConditionFactory()->from([
                    'numberRequired' => 1,
                    'conditions' => [
                        [
                            'type'      => 'self.status.skill',
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
                self::getConditionFactory()->from([
                    'numberRequired' => 1,
                    'conditions' => [
                        [
                            'type'      => 'self.status.skill',
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
                self::getConditionFactory()->from([
                    'numberRequired' => 1,
                    'conditions' => [
                        [
                            'type'     => 'self.ability.strength',
                            'condition' => 'value>10',
                            'is'        => true,
                        ],
                        [
                            'type'      => 'self.ability.agility',
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
                self::getConditionFactory()->from([
                    'numberRequired' => 2,
                    'conditions' => [
                        [
                            'type'      => 'self.ability.strength',
                            'condition' => 'value<=10',
                            'is'        => true,
                        ],
                        [
                            'type'      => 'self.ability.agility',
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
                self::getConditionFactory()->from([
                    'numberRequired' => 1,
                    'conditions' => [
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
                self::getConditionFactory()->from([
                    'numberRequired' => 1,
                    'conditions' => [
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
                self::getConditionFactory()->from([
                    'numberRequired' => 1,
                    'conditions' => [
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
     * @return array<string, array{0: Conditions|null, 1: EntityInterface, 2: EntityInterface, 3: class-string<\Throwable>}>
     */
    public static function evaluateExceptionDataProvider(): array
    {
        self::setUpBeforeClass();

        return [
            'evaluate with not supported type property' => [
                self::getConditionFactory()->from([
                    'numberRequired' => 1,
                    'conditions'     => [
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
            'evaluate required conditions based on status but status type does not exist' => [
                self::getConditionFactory()->from([
                    'numberRequired' => 1,
                    'conditions' => [
                        [
                            'type'      => 'self.status.unknown',
                            'condition' => 'name=test',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                StatusException::class,
            ],
            'evaluate required conditions based on element that is not supported' => [
                self::getConditionFactory()->from([
                    'numberRequired' => 1,
                    'conditions' => [
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
            'evaluate required conditions based on ability but property not supported' => [
                self::getConditionFactory()->from([
                    'numberRequired' => 1,
                    'conditions' => [
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
            'evaluate required conditions based on ability but ability not found' => [
                self::getConditionFactory()->from([
                    'numberRequired' => 1,
                    'conditions' => [
                        [
                            'type'      => 'self.ability.unknown',
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
                self::getConditionFactory()->from([
                    'numberRequired' => 1,
                    'conditions' => [
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
