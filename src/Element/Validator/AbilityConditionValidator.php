<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Validator;

use Velkuns\GameTextEngine\Element\Ability\AbilityInterface;
use Velkuns\GameTextEngine\Element\Condition\ConditionOperatorType;
use Velkuns\GameTextEngine\Element\Condition\ConditionParser;
use Velkuns\GameTextEngine\Exception\Element\UnsupportedConditionOperatorTypeException;
use Velkuns\GameTextEngine\Exception\Element\UnsupportedConditionPropertyException;

/**
 * @phpstan-import-type ConditionPartData from ConditionParser
 */
readonly class AbilityConditionValidator implements ValidatorInterface
{
    public function supports(string $type): bool
    {
        return \str_starts_with($type, 'ability.');
    }

    /**
     * @param list<ConditionPartData> $conditions
     */
    public function validate(string $type, object $element, array $conditions): bool
    {
        if (!$element instanceof AbilityInterface) {
            return false; // @codeCoverageIgnore
        }

        $evaluation = true;

        foreach ($conditions as ['property' => $property, 'operator' => $operator, 'value' => $value]) {
            $elementValue = $this->getElementValue($element, $property);
            $evaluation   = $evaluation && $this->isValid($elementValue, $operator, (int) $value);
        }

        //~ Return final evaluation condition part on whole object
        return $evaluation;
    }

    private function isValid(
        int $elementValue,
        ConditionOperatorType $operatorType,
        int $conditionValue,
    ): bool {

        return match ($operatorType) {
            ConditionOperatorType::Equal              => ($elementValue === $conditionValue),
            ConditionOperatorType::Different          => ($elementValue !== $conditionValue),
            ConditionOperatorType::LessThan           => ($elementValue < $conditionValue),
            ConditionOperatorType::LessOrEqualThan    => ($elementValue <= $conditionValue),
            ConditionOperatorType::GreaterThan        => ($elementValue > $conditionValue),
            ConditionOperatorType::GreaterOrEqualThan => ($elementValue >= $conditionValue),
            default => throw new UnsupportedConditionOperatorTypeException("Ability condition does not support $operatorType->name type"),
        };
    }

    private function getElementValue(AbilityInterface $ability, string $property): int
    {
        return match ($property) {
            'value' => $ability->getValue(),
            default => throw new UnsupportedConditionPropertyException(),
        };
    }
}
