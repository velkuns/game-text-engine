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
use Velkuns\GameTextEngine\Element\Entity\EntityStatuses;
use Velkuns\GameTextEngine\Element\Status\StatusInterface;
use Velkuns\GameTextEngine\Exception\Element\UnsupportedConditionOperatorTypeException;
use Velkuns\GameTextEngine\Exception\Element\UnsupportedConditionPropertyException;

/**
 * @phpstan-import-type ConditionPartData from ConditionParser
 */
readonly class StatusConditionValidator implements ValidatorInterface
{
    public function supports(string $type): bool
    {
        return \str_starts_with($type, 'status.');
    }

    /**
     * @param list<ConditionPartData> $conditions
     */
    public function validate(string $type, object $element, array $conditions): bool
    {
        if (!$element instanceof EntityStatuses) {
            return false; // @codeCoverageIgnore
        }

        $statusType = \explode('.', $type)[1] ?? '';

        foreach ($element->getAllFromType($statusType) as $status) {
            if ($this->validateItem($status, $conditions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param list<ConditionPartData> $conditions
     */
    private function validateItem(StatusInterface $status, array $conditions): bool
    {
        $evaluation = true;

        foreach ($conditions as ['property' => $property, 'operator' => $operator, 'value' => $value]) {
            $elementValue = $this->resolveValue($status, $property);
            $evaluation   = $evaluation && $this->isValid($elementValue, $operator, (string) $value);
        }

        //~ Return final evaluation condition part on whole object
        return $evaluation;
    }

    private function isValid(
        string $elementValue,
        ConditionOperatorType $operatorType,
        string $conditionValue,
    ): bool {

        return match ($operatorType) {
            ConditionOperatorType::Equal              => ($elementValue === $conditionValue),
            default => throw new UnsupportedConditionOperatorTypeException("Status condition does not support $operatorType->name type"),
        };
    }

    private function resolveValue(StatusInterface $status, string $property): string
    {
        return match (\strtolower($property)) {
            'name'     => $status->getName(),
            default    => throw new UnsupportedConditionPropertyException("Condition property '$property' is not supported."),
        };
    }
}
