<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Ability;

interface AbilityInterface extends \JsonSerializable
{
    public function getType(): AbilityType;

    public function getName(): string;

    public function getCurrent(): int;

    public function getMax(): int;

    public function getInitial(): int;

    public function getConstraints(): ConstraintsAbility;

    public function getRule(): ?string;
}
