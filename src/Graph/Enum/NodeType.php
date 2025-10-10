<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Graph\Enum;

enum NodeType: int
{
    case Root = 1;
    case Link = 2;
    case Leaf = 3;
}
