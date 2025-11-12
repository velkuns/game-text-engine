<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Modifier;

use Velkuns\GameTextEngine\Exception\Core\UnsupportedModifierProcessorPropertyException;
use Velkuns\GameTextEngine\Rpg\Attribute\AttributeInterface;

readonly class AttributeModifierProcessor implements ModifierProcessInterface
{
    public function supports(string $type): bool
    {
        return \str_starts_with($type, 'attribute.');
    }

    public function apply(string $type, object $element, int $value): void
    {
        if (!$element instanceof AttributeInterface) {
            return; // @codeCoverageIgnore
        }

        $property = \substr($type, (int) \strrpos($type, '.') + 1);

        match ($property) {
            'value' => $value > 0 ? $element->increase($value) : $element->decrease($value),
            default => throw new UnsupportedModifierProcessorPropertyException("Attribute property '$property' does not exist."),
        };
    }
}
