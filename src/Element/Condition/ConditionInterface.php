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

/**
 * @phpstan-type ConditionData array{
 *     type: string,
 *     name: string,
 *     operator: string,
 *     value: int,
 *     subType?: string|null,
 *     equipped?: bool|null,
 *     flags?: int|null,
 * }
 */
interface ConditionInterface
{
    public function getType(): ConditionType;
    public function getName(): string;
    public function getOperator(): ConditionOperatorType;
    public function getValue(): int;
    public function getSubType(): ?string;
    public function isEquipped(): ?bool;
    public function getFlags(): ?int;

    public function evaluate(EntityInterface $entity): bool;
}
