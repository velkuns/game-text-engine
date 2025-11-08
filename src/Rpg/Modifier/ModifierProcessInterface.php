<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Modifier;

interface ModifierProcessInterface
{
    public function supports(string $type): bool;

    public function apply(string $type, object $element, int $value): void;
}
