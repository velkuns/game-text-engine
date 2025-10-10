<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Graph\Node;

use Velkuns\GameTextEngine\Graph\Enum\NodeType;
use Velkuns\GameTextEngine\Graph\Node\ChoiceNode;

readonly class TextNode
{
    /**
     * @param list<ChoiceNode> $choices
     */
    public function __construct(
        public int $id,
        public string $content,
        public NodeType $type,
        public array $choices = [],
    ) {}
}
