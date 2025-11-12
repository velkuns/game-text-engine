<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Core\Evaluator;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Core\Evaluator\Evaluator;
use Velkuns\GameTextEngine\Exception\Core\EvaluatorException;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;
use Velkuns\GameTextEngine\Tests\Helper\ResolverTrait;

class EvaluatorTest extends TestCase
{
    use EntityTrait;
    use ResolverTrait;

    /**
     * @param array<string, int|float>|null $context
     */
    #[DataProvider('getValidRulesProvider')]
    public function testEvaluate(string $rule, string $renderedRule, ?array $context, int|float|bool $expected): void
    {
        $evaluator = new Evaluator(self::getValueResolverHandler());

        $playerAttacker = self::getPlayer();
        $goblinDefender = self::getGoblin();

        $render = $evaluator->render($rule, $playerAttacker, $goblinDefender, context: $context);
        $result = $evaluator->evaluate($rule, $playerAttacker, $goblinDefender, context: $context);

        self::assertSame($renderedRule, $render);
        self::assertEqualsWithDelta($expected, $result, 0.01);
    }

    /**
     * @param class-string<\Throwable> $exception
     */
    #[DataProvider('getInvalidRulesProvider')]
    public function testEvaluateButFailed(string $rule, string $exception, int $code): void
    {
        $evaluator = new Evaluator(self::getValueResolverHandler());

        $playerAttacker = self::getPlayer();

        self::expectException($exception);
        self::expectExceptionCode($code);
        $evaluator->evaluate($rule, $playerAttacker);
    }

    /**
     * @return array<string, array{
     *     rule: string,
     *     renderedRule: string,
     *     context: array<string, int|float>|null,
     *     expected: int|float|bool
     * }>
     */
    public static function getValidRulesProvider(): array
    {
        return [
            'simple rule' => [
                'rule'         => '(((1 + 2) * 2) / 2) - 1',
                'renderedRule' => '(((1 + 2) * 2) / 2) - 1',
                'context'      => null,
                'expected'     => 2,
            ],
            'rule with attribute' => [
                'rule'         => '(attacker.attribute.attack.value / (defender.attribute.defense.value * 2)) * 100',
                'renderedRule' => '(25 / (18 * 2)) * 100',
                'context'      => null,
                'expected'     => 69.44,
            ],
            'rule with attributes and equipped weapon' => [
                'rule'         => '(attacker.attribute.strength.value * 2) - defender.attribute.endurance.value + attacker.weapon.equipped.damages',
                'renderedRule' => '(10 * 2) - 8 + 2',
                'context'      => ['combat.hit_chance' => 10],
                'expected'     => 14,
            ],
            'rule with random int' => [
                'rule'         => 'self.roll(100)',
                'renderedRule' => '38', // first call random via render()
                'context'      => null,
                'expected'     => 32,   // second call to random via evaluate()
            ],
            'rule with random float' => [
                'rule'         => 'self.roll(1)',     // float
                'renderedRule' => '0.67996817849154', // first call random via render()
                'context'      => null,
                'expected'     => 0.32555439438137,   // second call to random via evaluate()
            ],
            'rule with context used in rule' => [
                'rule'         => 'attacker.roll(100) < combat.hit_chance',
                'renderedRule' => '38 < 10',
                'context'      => ['combat.hit_chance' => 10],
                'expected'     => false, // Here roll result is 32, so no luck :)
            ],
        ];
    }

    /**
     * @return array<string, array{
     *     rule: string,
     *     exception: class-string<\Throwable>,
     *     code: int,
     * }>
     */
    public static function getInvalidRulesProvider(): array
    {
        return [
            'broken rule' => [
                'rule'         => '((1 +2)',
                'exception'    => EvaluatorException::class,
                'code'         => 3000,
            ],
            'invalid character in rule' => [
                'rule'         => 'y(1 +2)',
                'exception'    => EvaluatorException::class,
                'code'         => 3001,
            ],
            'defender in rule, but no enemy provided' => [
                'rule'         => '(attacker.attribute.attack.value / (defender.attribute.defense.value * 2)) * 100',
                'exception'    => EvaluatorException::class,
                'code'         => 3003,
            ],
        ];
    }
}
