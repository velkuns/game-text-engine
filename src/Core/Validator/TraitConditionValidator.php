<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Validator;

use Velkuns\GameTextEngine\Core\Condition\ConditionOperatorType;
use Velkuns\GameTextEngine\Core\Condition\ConditionParser;
use Velkuns\GameTextEngine\Exception\Core\UnsupportedConditionOperatorTypeException;
use Velkuns\GameTextEngine\Exception\Core\UnsupportedConditionPropertyException;
use Velkuns\GameTextEngine\Rpg\Entity\EntityTraits;
use Velkuns\GameTextEngine\Rpg\Traits\TraitInterface;

/**
 * @phpstan-import-type ConditionPartData from ConditionParser
 */
readonly class TraitConditionValidator implements ValidatorInterface
{
    public function supports(string $type): bool
    {
        return \str_starts_with($type, 'trait.');
    }

    /**
     * @param list<ConditionPartData> $conditions
     */
    public function validate(string $type, object $element, array $conditions): bool
    {
        if (!$element instanceof EntityTraits) {
            return false; // @codeCoverageIgnore
        }

        $traitType = \explode('.', $type)[1] ?? '';

        foreach ($element->getAllFromType($traitType) as $trait) {
            if ($this->validateItem($trait, $conditions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param list<ConditionPartData> $conditions
     */
    private function validateItem(TraitInterface $trait, array $conditions): bool
    {
        $evaluation = true;

        foreach ($conditions as ['property' => $property, 'operator' => $operator, 'value' => $value]) {
            $elementValue = $this->resolveValue($trait, $property);
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
            default => throw new UnsupportedConditionOperatorTypeException("Trait condition does not support $operatorType->name type"),
        };
    }

    private function resolveValue(TraitInterface $trait, string $property): string
    {
        return match (\strtolower($property)) {
            'name'     => $trait->getName(),
            default    => throw new UnsupportedConditionPropertyException("Condition property '$property' is not supported."),
        };
    }
}
