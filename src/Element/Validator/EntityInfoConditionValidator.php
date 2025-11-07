<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Validator;

use Velkuns\GameTextEngine\Element\Condition\ConditionOperatorType;
use Velkuns\GameTextEngine\Element\Condition\ConditionParser;
use Velkuns\GameTextEngine\Element\Entity\EntityInfo;
use Velkuns\GameTextEngine\Exception\Element\UnsupportedConditionOperatorTypeException;
use Velkuns\GameTextEngine\Exception\Element\UnsupportedConditionPropertyException;

/**
 * @phpstan-import-type ConditionPartData from ConditionParser
 */
readonly class EntityInfoConditionValidator implements ValidatorInterface
{
    public function supports(string $type): bool
    {
        return $type === 'info';
    }

    /**
     * @param list<ConditionPartData> $conditions
     */
    public function validate(string $type, object $element, array $conditions): bool
    {
        if (!$element instanceof EntityInfo) {
            return false; // @codeCoverageIgnore
        }

        $evaluation = true;

        foreach ($conditions as ['property' => $property, 'operator' => $operator, 'value' => $value]) {
            $elementValue = $this->resolveValue($element, $property);
            $value        = \is_int($elementValue) ? (int) $value : (string) $value;
            $evaluation   = $evaluation && $this->isValid($elementValue, $operator, $value);
        }

        //~ Return final evaluation condition part on whole object
        return $evaluation;
    }

    private function isValid(
        string|int $elementValue,
        ConditionOperatorType $operatorType,
        int|string $conditionValue,
    ): bool {

        return match ($operatorType) {
            ConditionOperatorType::Equal              => ($elementValue === $conditionValue),
            ConditionOperatorType::Different          => ($elementValue !== $conditionValue),
            ConditionOperatorType::LessThan           => ($elementValue < $conditionValue),
            ConditionOperatorType::LessOrEqualThan    => ($elementValue <= $conditionValue),
            ConditionOperatorType::GreaterThan        => ($elementValue > $conditionValue),
            ConditionOperatorType::GreaterOrEqualThan => ($elementValue >= $conditionValue),
            default => throw new UnsupportedConditionOperatorTypeException("EntityInfo condition does not support $operatorType->name type"),
        };
    }

    private function resolveValue(EntityInfo $entityInfo, string $property): string|int
    {
        return match ($property) {
            'size'  => $entityInfo->size,
            'race'  => $entityInfo->race,
            'level' => $entityInfo->level,
            default    => throw new UnsupportedConditionPropertyException(),
        };
    }
}
