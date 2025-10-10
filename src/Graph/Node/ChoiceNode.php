<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Graph\Node;

use Velkuns\GameTextEngine\Graph\Condition\ConditionInterface;

readonly class ChoiceNode
{
    public function __construct(
        public string $content,
        public TextNode $text,
        public ConditionInterface $condition,
    ) {}

    /**
     * @param array<mixed> $context
     */
    public function available(array $context = []): bool
    {
        return $this->condition->evaluate($context);
    }
}
