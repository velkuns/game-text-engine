<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Validator;

use Velkuns\GameTextEngine\Core\Prerequisite\ConditionOperatorType;
use Velkuns\GameTextEngine\Core\Prerequisite\ConditionParser;
use Velkuns\GameTextEngine\Exception\Core\UnsupportedConditionOperatorTypeException;
use Velkuns\GameTextEngine\Exception\Core\UnsupportedConditionPropertyException;
use Velkuns\GameTextEngine\Rpg\Entity\EntityAlterations;
use Velkuns\GameTextEngine\Rpg\Alteration\AlterationInterface;

/**
 * @phpstan-import-type ConditionPartData from ConditionParser
 */
readonly class AlterationConditionValidator implements ValidatorInterface
{
    public function supports(string $type): bool
    {
        return \str_starts_with($type, 'alteration.');
    }

    /**
     * @param list<ConditionPartData> $conditions
     */
    public function validate(string $type, object $element, array $conditions): bool
    {
        if (!$element instanceof EntityAlterations) {
            return false; // @codeCoverageIgnore
        }

        $alterationType = \explode('.', $type)[1] ?? '';

        foreach ($element->getAllFromType($alterationType) as $alteration) {
            if ($this->validateItem($alteration, $conditions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param list<ConditionPartData> $conditions
     */
    private function validateItem(AlterationInterface $alteration, array $conditions): bool
    {
        $evaluation = true;

        foreach ($conditions as ['property' => $property, 'operator' => $operator, 'value' => $value]) {
            $elementValue = $this->resolveValue($alteration, $property);
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
            default => throw new UnsupportedConditionOperatorTypeException("Alteration condition does not support $operatorType->name type"),
        };
    }

    private function resolveValue(AlterationInterface $trait, string $property): string
    {
        return match (\strtolower($property)) {
            'name'     => $trait->getName(),
            default    => throw new UnsupportedConditionPropertyException("Condition property '$property' is not supported."),
        };
    }
}
