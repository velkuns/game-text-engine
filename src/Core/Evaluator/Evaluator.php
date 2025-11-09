<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Evaluator;

use Velkuns\GameTextEngine\Core\Resolver\ValueResolverHandler;
use Velkuns\GameTextEngine\Exception\Core\EvaluatorException;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;
use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

readonly class Evaluator
{
    private const string PATTERN_NOT_MATH_CHAR = '#[^[( )/*0-9.+<>=!-]+#';
    private const string PATTERN_TARGET_TYPE = '#(?P<target>self|enemy|attacker|defender)\.[a-z0-9.(,)_]+#';

    public function __construct(private ValueResolverHandler $valueResolverHandler) {}

    /**
     * @param list<Modifier> $playerOrAttackerModifiers
     * @param list<Modifier> $enemyOrDefenderModifiers
     * @param array<string, int|float>|null $context Contains some elements to replace, depends the context
     */
    public function evaluate(
        string $rule,
        EntityInterface $playerOrAttacker,
        ?EntityInterface $enemyOrDefender = null,
        array $playerOrAttackerModifiers = [],
        array $enemyOrDefenderModifiers = [],
        ?array $context = null,
    ): int|float|bool {
        $renderedRule = $this->render($rule, $playerOrAttacker, $enemyOrDefender, $playerOrAttackerModifiers, $enemyOrDefenderModifiers, $context);

        return $this->eval($renderedRule);
    }

    /**
     * @param list<Modifier> $playerOrAttackerModifiers
     * @param list<Modifier> $enemyOrDefenderModifiers
     * @param array<string, int|float>|null $context Contains some elements to replace, depends the context
     */
    public function render(
        string $rule,
        EntityInterface $playerOrAttacker,
        ?EntityInterface $enemyOrDefender = null,
        array $playerOrAttackerModifiers = [],
        array $enemyOrDefenderModifiers = [],
        ?array $context = null,
    ): string {

        //~ Start by replacing context strings
        foreach ($context ?? [] as $name => $value) {
            $rule = \str_replace($name, (string) $value, $rule);
        }

        $found = \preg_match_all(self::PATTERN_TARGET_TYPE, $rule, $matches);

        if ($found === false) {
            throw new EvaluatorException('Failed to parse rule "' . $rule . '".', 3002); // @codeCoverageIgnore
        }

        if ($found === 0) {
            return $rule; // Simple rule
        }

        foreach ($matches['target'] as $index => $target) {
            $entity = match ($target) {
                'self', 'attacker'  => $playerOrAttacker,
                'enemy', 'defender' => $enemyOrDefender,
            };
            $modifiers = match ($target) {
                'self', 'attacker'  => $playerOrAttackerModifiers,
                'enemy', 'defender' => $enemyOrDefenderModifiers,
            };
            $type = $matches[0][$index];

            if ($entity === null) {
                throw new EvaluatorException('Missing entity context to evaluate rule (defender or enemy)', 3003);
            }

            //~ Resolve value
            $value = $this->valueResolverHandler->handle($type, $entity, $modifiers);

            //~ Replace value in rule
            $rule = \str_replace($type, (string) $value, $rule);
        }

        return $rule;
    }

    private function eval(string $rule): int|float|bool
    {
        /** @var int|float|bool $result */
        $result = 0;

        //~ Be sure the rule is math expression
        $this->assertIsMathExpression($rule);

        try {
            eval("\$result = $rule;");
            return $result;
        } catch (\Throwable $throwable) {
            throw new EvaluatorException('Failed to evaluate the rule "' . $rule . '".', 3000, $throwable);
        }
    }

    private function assertIsMathExpression(string $rule): void
    {
        $found = \preg_match(self::PATTERN_NOT_MATH_CHAR, $rule);

        if ($found > 0) {
            throw new EvaluatorException('Not a valid math rule (contains some unauthorized characters).', 3001);
        }
    }
}
