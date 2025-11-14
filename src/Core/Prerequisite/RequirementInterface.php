<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Prerequisite;

use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;

/**
 * @phpstan-type RequirementData array{
 *     type: string,
 *     condition: string,
 *     is: bool,
 * }
 */
interface RequirementInterface
{
    public function getType(): string;

    public function getCondition(): string;

    public function is(): bool;

    public function evaluate(EntityInterface $entity): bool;

    /**
     * @return RequirementData
     */
    public function jsonSerialize(): array;

    public function clone(): self;
}
