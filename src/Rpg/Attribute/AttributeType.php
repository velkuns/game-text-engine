<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Attribute;

enum AttributeType: string
{
    case Simple = 'simple';
    case Compound = 'compound';
}
