<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Graph\Node;

readonly class ActionNode
{
    public function __construct(
        public int $id,
        public string $content,
    ) {}
}
