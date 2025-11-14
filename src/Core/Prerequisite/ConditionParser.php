<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Prerequisite;

/**
 * @phpstan-type ConditionPartData array{property: string, operator: ConditionOperatorType, value: string|int|float|bool|null}
 */
readonly class ConditionParser
{
    private const string PATTERN = '`(?P<property>[a-zA-Z]+) *(?P<operator>>=|<=|!=|=|>|<|&) *(?P<value>.+)`';

    /**
     * @return list<ConditionPartData>
     */
    public function parse(string $condition): array
    {
        $conditionParts = array_map(trim(...), \explode(';', $condition));

        $parsedCondition = [];
        foreach ($conditionParts as $conditionPart) {
            if (\preg_match(self::PATTERN, $conditionPart, $matches) > 0) {
                $parsedCondition[] = [
                    'property' => $matches['property'],
                    'operator' => ConditionOperatorType::from($matches['operator']),
                    'value'    => $this->cast($matches['value']),
                ];
            }
        }

        return $parsedCondition;
    }

    private function cast(string $value): string|int|float|bool|null
    {
        return match (true) {
            \is_numeric($value) && \str_contains($value, '.') => (float) $value,
            \is_numeric($value) => (int) $value,
            $value === 'true'   => true,
            $value === 'false'  => false,
            $value === 'null'   => null,
            default             => $value,
        };
    }
}
