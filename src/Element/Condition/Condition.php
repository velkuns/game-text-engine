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

/**
 * @phpstan-import-type ConditionData from ConditionInterface
 */
readonly class Condition implements ConditionInterface
{
    public function __construct(
        private string $type,
        private string $name,
        private ConditionOperatorType $operator,
        private int $value,
        private ?string $subType = null,
        private ?bool $isEquipped = null,
        private ?int $flags = null,
    ) {}

    public function getType(): string
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
            'self.ability'             => $entity->getAbilities()->get($this->getName())?->getCurrent() ?? 0,
            'self.statuses.skill'      => $entity->hasStatus('skill', $this->getName()) ? 1 : 0,
            'self.statuses.state'      => $entity->hasStatus('state', $this->getName()) ? 1 : 0,
            'self.statuses.blessing'   => $entity->hasStatus('blessing', $this->getName()) ? 1 : 0,
            'self.statuses.curse'      => $entity->hasStatus('curse', $this->getName()) ? 1 : 0,
            'self.statuses.title'      => $entity->hasStatus('title', $this->getName()) ? 1 : 0,
            'enemy.info.race'           => $entity->getInfo()->race === $this->getName() ? 1 : 0,
            'enemy.info.size'           => $entity->getInfo()->size === $this->getName() ? 1 : 0,
            'self.inventory.item'      => $this->isValidItem($entity->getInventory()) ? 1 : 0,
            default                      => 0, // Unknown type, return 0
        };

        return $this->match($entityValue);
    }

    private function isValidItem(EntityInventory $inventory): bool
    {
        foreach ($inventory->items as $item) {
            $matchFull = 0;
            $match     = 0;

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

            if ($match === $matchFull) {
                return true;
            }
        }

        return false;
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
            'type'     => $this->type,
            'name'     => $this->name,
            'operator' => $this->operator->value,
            'value'    => $this->value,
            'subType'  => $this->subType,
            'equipped' => $this->isEquipped,
            'flags'    => $this->flags,
        ];
    }
}
