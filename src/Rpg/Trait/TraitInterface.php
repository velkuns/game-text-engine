<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Trait;

use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

/**
 * @phpstan-import-type ModifierData from Modifier
 * @phpstan-type TraitData array{
 *    type: string,
 *    name: string,
 *    description: string,
 *    modifiers: list<ModifierData>,
 * }
 */
interface TraitInterface extends \JsonSerializable
{
    public function getType(): string;

    public function getName(): string;

    public function getDescription(): string;

    /**
     * @return Modifier[]
     */
    public function getModifiers(): array;

    public function clone(): self;

    /**
     * @return TraitData
     */
    public function jsonSerialize(): array;
}
