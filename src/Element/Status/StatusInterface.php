<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Status;

use Velkuns\GameTextEngine\Element\Condition\Conditions;
use Velkuns\GameTextEngine\Element\Modifier\Modifier;

/**
 * @phpstan-import-type ConditionsData from Conditions
 * @phpstan-import-type ModifierData from Modifier
 * @phpstan-type StatusData array{
 *    type: string,
 *    name: string,
 *    description: string,
 *    modifiers: list<ModifierData>,
 *    conditions?: ConditionsData|null,
 *    durationTurns?: int,
 *    remainingTurns?: int,
 * }
 */
interface StatusInterface extends \JsonSerializable
{
    public function getType(): string;

    public function getName(): string;

    public function getDescription(): string;

    public function getConditions(): ?Conditions;

    /**
     * @return Modifier[]
     */
    public function getModifiers(): array;

    public function getDurationTurns(): int;

    public function getRemainingTurns(): int;

    public function decreaseRemainingTurns(): self;

    public function clone(): self;

    /**
     * @return StatusData
     */
    public function jsonSerialize(): array;
}
