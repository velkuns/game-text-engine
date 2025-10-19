<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Condition;

enum ConditionType: string
{
    case Ability = 'ability';
    case Skill = 'skill';
    case State = 'state';
    case Blessing = 'blessing';
    case Curse = 'curse';
    case Title = 'title';
    case Item = 'item';
    case EntitySize = 'entity_size';
    case EntityRace = 'entity_race';

    public function isPlayerCondition(): bool
    {
        return \in_array(
            $this,
            [
                self::Ability,
                self::Skill,
                self::State,
                self::Blessing,
                self::Curse,
                self::Title,
                self::Item,
            ],
            true,
        );
    }
}
