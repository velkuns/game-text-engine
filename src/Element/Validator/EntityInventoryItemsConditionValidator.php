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
use Velkuns\GameTextEngine\Element\Entity\EntityInventory;
use Velkuns\GameTextEngine\Element\Exception\UnsupportedConditionPropertyException;
use Velkuns\GameTextEngine\Element\Item\ItemInterface;

/**
 * @phpstan-import-type ConditionPartData from ConditionParser
 */
readonly class EntityInventoryItemsConditionValidator implements ValidatorInterface
{
    public function supports(string $type): bool
    {
        return $type === 'inventory.item';
    }

    /**
     * @param list<ConditionPartData> $conditions
     */
    public function validate(string $type, object $element, array $conditions): bool
    {
        if (!$element instanceof EntityInventory) {
            return false; // @codeCoverageIgnore
        }

        foreach ($element->items as $item) {
            if ($this->validateItem($item, $conditions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param list<ConditionPartData> $conditions
     */
    private function validateItem(ItemInterface $item, array $conditions): bool
    {
        $evaluation = true;

        foreach ($conditions as ['property' => $property, 'operator' => $operator, 'value' => $value]) {
            $elementValue = $this->resolveValue($item, $property);
            $evaluation   = $evaluation && $this->isValid($elementValue, $operator, $value);
        }

        //~ Return final evaluation condition part on whole object
        return $evaluation;
    }

    private function isValid(
        string|int|bool|null $elementValue,
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

    private function resolveValue(ItemInterface $item, string $property): string|int|bool|null
    {
        return match (\strtolower($property)) {
            'name'     => $item->getName(),
            'subtype'  => $item->getSubType(),
            'flags'    => $item->getFlags(),
            'equipped' => $item->equipped(),
            default    => throw new UnsupportedConditionPropertyException("Condition property '$property' is not supported."),
        };
    }
}
