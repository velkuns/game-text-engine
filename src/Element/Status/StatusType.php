<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Status;

enum StatusType: string
{
    case Skill = 'skill';
    case State = 'state';
    case Blessing = 'blessing';
    case Curse = 'curse';
    case Title = 'title';
}
