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
use Velkuns\GameTextEngine\Element\Condition\ConditionParser;
use Velkuns\GameTextEngine\Element\Condition\Conditions;
use Velkuns\GameTextEngine\Element\Condition\ConditionValidator;
use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Exception\TypeElementResolveException;
use Velkuns\GameTextEngine\Element\Factory\AbilityFactory;
use Velkuns\GameTextEngine\Element\Factory\ConditionsFactory;
use Velkuns\GameTextEngine\Element\Factory\EntityFactory;
use Velkuns\GameTextEngine\Element\Factory\ItemFactory;
use Velkuns\GameTextEngine\Element\Factory\ModifierFactory;
use Velkuns\GameTextEngine\Element\Factory\StatusFactory;
use Velkuns\GameTextEngine\Element\Resolver\TypeElementResolver;
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
        EntityInterface $enemy,
        bool $evaluation,
    ): void {
        self::assertSame($evaluation, $conditions->evaluate($player, $enemy));
    }

    #[DataProvider('evaluateExceptionDataProvider')]
    public function testEvaluateThatThrowException(
        Conditions $conditions,
        EntityInterface $player,
        EntityInterface $enemy,
        int $code,
    ): void {
        self::expectException(TypeElementResolveException::class);
        self::expectExceptionCode($code);
        $conditions->evaluate($player, $enemy);
    }

    /**
     * @return array<string, array{0: Conditions|null, 1: EntityInterface, 2: EntityInterface, 3: bool}>
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
                            'type'      => 'self.abilities.bases.strength',
                            'condition' => 'value >=  10 ',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                true,
            ],
            'evaluate required 1 condition with list of 1 conditions (use get method)' => [
                self::getConditionFactory()->from([
                    'numberRequired' => 1,
                    'conditions' => [
                        [
                            'type'      => 'self.abilities.strength',
                            'condition' => 'value >=  10 ',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                true,
            ],
            'evaluate required 1 condition with list of 1 conditions but evaluation failed' => [
                self::getConditionFactory()->from([
                    'numberRequired' => 1,
                    'conditions' => [
                        [
                            'type'      => 'self.statuses.skills',
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
                            'type'     => 'self.abilities.bases.strength',
                            'condition' => 'value>10',
                            'is'        => true,
                        ],
                        [
                            'type'      => 'self.abilities.bases.agility',
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
                            'type'      => 'self.abilities.bases.strength',
                            'condition' => 'value<=10',
                            'is'        => true,
                        ],
                        [
                            'type'      => 'self.abilities.bases.agility',
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
                            'type'      => 'self.inventory.items',
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
                            'type'      => 'self.inventory.items',
                            'condition' => 'name=The Sword;subType=sword;equipped=false;flags&4',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                false,
            ],
        ];
    }

    /**
     * @return array<string, array{0: Conditions|null, 1: EntityInterface, 2: EntityInterface, 3: int}>
     */
    public static function evaluateExceptionDataProvider(): array
    {
        self::setUpBeforeClass();

        return [
            'evaluate with not objects in middle of type' => [
                self::getConditionFactory()->from([
                    'numberRequired' => 1,
                    'conditions'     => [
                        [
                            'type'      => 'self.abilities.bases.strength.value.deeper',
                            'condition' => 'value >=  10 ',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                1100,
            ],
            //'evaluate with array but no key exists - no concret example' => [
            //    self::$conditionFactory->from([
            //        'numberRequired' => 1,
            //        'conditions'     => [
            //            [
            //                'type'      => 'self.abilities.bases.strength.value',
            //                'condition' => 'value >=  10 ',
            //                'is'        => true,
            //            ],
            //        ],
            //    ]),
            //    self::getPlayer(),
            //    self::getGoblin(),
            //    1101,
            //],
            'evaluate with not found type property' => [
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
                1102,
            ],
            'evaluate with not enough part in type' => [
                self::getConditionFactory()->from([
                    'numberRequired' => 1,
                    'conditions'     => [
                        [
                            'type'      => 'self',
                            'condition' => 'value >=  10 ',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                1103,
            ],
            'evaluate with not object as end part' => [
                self::getConditionFactory()->from([
                    'numberRequired' => 1,
                    'conditions'     => [
                        [
                            'type'      => 'self.abilities.strength.value',
                            'condition' => 'value >=  10 ',
                            'is'        => true,
                        ],
                    ],
                ]),
                self::getPlayer(),
                self::getGoblin(),
                1104,
            ],
        ];
    }
}
