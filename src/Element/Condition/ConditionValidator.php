<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Condition;

/**
 * @phpstan-import-type ConditionPartData from ConditionParser
 */
readonly class ConditionValidator implements ConditionValidatorInterface
{
    /**
     * @param list<ConditionPartData> $conditions
     */
    public function validate(object $element, array $conditions): bool
    {
        $evaluation = true;

        foreach ($conditions as $condition) {
            //~ Resolve element value (fetch from property, method...
            $elementValue = $this->resolve($element, $condition['property']);

            $isValid = $this->isValid($elementValue, $condition['operator'], $condition['value']);

            //~ Then evaluate condition part
            $evaluation = $evaluation && $isValid;
        }

        //~ Return final evaluation condition part on whole object
        return $evaluation;
    }

    private function isValid(
        string|int|float|bool|null $elementValue,
        ConditionOperatorType $operatorType,
        string|int|float|bool|null $conditionValue,
    ): bool {

        return match ($operatorType) {
            ConditionOperatorType::Equal              => ($elementValue === $conditionValue),
            ConditionOperatorType::Different          => ($elementValue !== $conditionValue),
            ConditionOperatorType::LessThan           => ($elementValue < $conditionValue),
            ConditionOperatorType::LessOrEqualThan    => ($elementValue <= $conditionValue),
            ConditionOperatorType::GreaterThan        => ($elementValue > $conditionValue),
            ConditionOperatorType::GreaterOrEqualThan => ($elementValue >= $conditionValue),
            ConditionOperatorType::HasFlags           => ((int) $conditionValue === ((int) $conditionValue & (int) $elementValue)),
        };
    }

    private function resolve(object $element, string $property): string|int|float|bool|null
    {
        $reflectionClass = new \ReflectionClass($element);

        //~ Try access to the property directly
        if ($reflectionClass->hasProperty($property) && $reflectionClass->getProperty($property)->isPublic()) {
            $elementValue = $element->{$property};

            return \is_scalar($elementValue) ? $elementValue : null;
        }

        //~ Try access via getter method with same property name
        if ($reflectionClass->hasMethod($property) && $reflectionClass->getMethod($property)->isPublic()) {
            $elementValue = $element->{$property}();

            return \is_scalar($elementValue) ? $elementValue : null;
        }

        //~ Try access via getter method
        $getter = 'get' . \ucfirst($property);
        if ($reflectionClass->hasMethod($getter) && $reflectionClass->getMethod($getter)->isPublic()) {
            $elementValue = $element->{$getter}();

            return \is_scalar($elementValue) ? $elementValue : null;
        }

        return null;
    }
}
