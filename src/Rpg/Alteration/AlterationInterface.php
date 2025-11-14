<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Alteration;

use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

/**
 * @phpstan-import-type ModifierData from Modifier
 * @phpstan-import-type AlterationDurationData from AlterationDuration
 * @phpstan-type AlterationData array{
 *    type: string,
 *    name: string,
 *    description: string,
 *    modifiers: list<ModifierData>,
 *    duration: AlterationDurationData,
 * }
 */
interface AlterationInterface extends \JsonSerializable
{
    public function getType(): string;

    public function getName(): string;

    public function getDescription(): string;

    /**
     * @return Modifier[]
     */
    public function getModifiers(): array;

    public function isActive(): bool;

    public function getDuration(): AlterationDuration;

    public function clone(): self;

    /**
     * @return AlterationData
     */
    public function jsonSerialize(): array;
}
