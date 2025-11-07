<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Modifier;

use Velkuns\GameTextEngine\Element\Ability\AbilityInterface;
use Velkuns\GameTextEngine\Exception\Element\UnsupportedModifierProcessorPropertyException;

readonly class AbilityModifierProcessor implements ModifierProcessInterface
{
    public function supports(string $type): bool
    {
        return \str_starts_with($type, 'ability.');
    }

    public function apply(string $type, object $element, int $value): void
    {
        if (!$element instanceof AbilityInterface) {
            return; // @codeCoverageIgnore
        }

        $property = \substr($type, (int) \strrpos($type, '.') + 1);

        match ($property) {
            'value' => $value > 0 ? $element->increase($value) : $element->decrease($value),
            default => throw new UnsupportedModifierProcessorPropertyException("Ability property '$property' does not exist."),
        };
    }
}
