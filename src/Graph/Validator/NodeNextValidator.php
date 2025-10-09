<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Graph\Validator;

use Velkuns\GameTextEngine\Graph\Node\ChoiceNode;
use Velkuns\GameTextEngine\Graph\Node\TextNode;

readonly class NodeNextValidator
{
    /**
     * @param array<mixed> $context
     */
    public function validate(TextNode $origin, int $currentTextId, array $context = []): bool
    {
        $choices = \array_filter(
            $origin->choices,
            function (ChoiceNode $choice) use ($currentTextId, $context) {
                return $choice->text->id === $currentTextId && $choice->available($context);
            },
        );

        return $choices !== [];
    }
}
