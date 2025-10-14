<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Condition;

use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Entity\EntityInventory;
use Velkuns\GameTextEngine\Element\Status\StatusType;

/**
 * @phpstan-import-type ConditionData from ConditionInterface
 */
readonly class Condition implements ConditionInterface
{
    public function __construct(
        private ConditionType $type,
        private string $name,
        private ConditionOperatorType $operator,
        private int $value,
        private ?string $subType = null,
        private ?bool $isEquipped = null,
        private ?int $flags = null,
    ) {}

    public function getType(): ConditionType
    {
        return $this->type;
    }

    public function getOperator(): ConditionOperatorType
    {
        return $this->operator;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSubType(): ?string
    {
        return $this->subType;
    }

    public function isEquipped(): ?bool
    {
        return $this->isEquipped;
    }

    public function getFlags(): ?int
    {
        return $this->flags;
    }

    public function evaluate(EntityInterface $entity): bool
    {
        $entityValue = match ($this->getType()) {
            ConditionType::Ability    => $entity->getAbilities()->get($this->getName())?->getCurrent() ?? 0,
            ConditionType::Skill      => $entity->hasStatus(StatusType::Skill, $this->getName()) ? 1 : 0,
            ConditionType::State      => $entity->hasStatus(StatusType::State, $this->getName()) ? 1 : 0,
            ConditionType::Blessing   => $entity->hasStatus(StatusType::Blessing, $this->getName()) ? 1 : 0,
            ConditionType::Curse      => $entity->hasStatus(StatusType::Curse, $this->getName()) ? 1 : 0,
            ConditionType::Title      => $entity->hasStatus(StatusType::Title, $this->getName()) ? 1 : 0,
            ConditionType::EntityRace => $entity->getInfo()->race === $this->getName() ? 1 : 0,
            ConditionType::EntitySize => $entity->getInfo()->size === $this->getName() ? 1 : 0,
            ConditionType::Item       => $this->isValidItem($entity->getInventory()) ? 1 : 0,
        };

        return $this->match($entityValue);
    }

    private function isValidItem(EntityInventory $inventory): bool
    {
        $matchFull = 0;
        $match     = 0;
        foreach ($inventory->items as $item) {
            if ($this->getName() !== '') {
                $matchFull++;
                $match += $item->getName() === $this->getName() ? 1 : 0;
            }

            if ($this->isEquipped() !== null) {
                $matchFull++;
                $match += $item->isEquipped() === $this->isEquipped() ? 1 : 0;
            }

            if ($this->getSubType() !== null) {
                $matchFull++;
                $match += $item->getSubType() === $this->getSubType() ? 1 : 0;
            }

            if ($this->getFlags() !== null) {
                $matchFull++;
                $match += ($item->getFlags() & $this->getFlags()) === $this->getFlags() ? 1 : 0;
            }

            if ($match < $matchFull) {
                return false;
            }
        }

        return true;
    }

    private function match(int $value): bool
    {
        return match ($this->getOperator()) {
            ConditionOperatorType::Equal              => ($value === $this->getValue()),
            ConditionOperatorType::Different          => ($value !== $this->getValue()),
            ConditionOperatorType::LessThan           => ($value < $this->getValue()),
            ConditionOperatorType::LessOrEqualThan    => ($value <= $this->getValue()),
            ConditionOperatorType::GreaterThan        => ($value > $this->getValue()),
            ConditionOperatorType::GreaterOrEqualThan => ($value >= $this->getValue()),
        };
    }

    /**
     * @return ConditionData
     */
    public function jsonSerialize(): array
    {
        return [
            'type'     => $this->type->value,
            'name'     => $this->name,
            'operator' => $this->operator->value,
            'value'    => $this->value,
            'subType'  => $this->subType,
            'equipped' => $this->isEquipped,
            'flags'    => $this->flags,
        ];
    }
}
