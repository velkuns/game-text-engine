<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Helper;

use Velkuns\GameTextEngine\Element\Validator\AbilityConditionValidator;
use Velkuns\GameTextEngine\Element\Validator\EntityInfoConditionValidator;
use Velkuns\GameTextEngine\Element\Validator\EntityInventoryItemsConditionValidator;
use Velkuns\GameTextEngine\Element\Validator\StatusConditionValidator;
use Velkuns\GameTextEngine\Element\Validator\ValidatorHandler;

trait ValidatorTrait
{
    private static function getValidatorHandler(): ValidatorHandler
    {
        $validators = [
            new AbilityConditionValidator(),
            new EntityInfoConditionValidator(),
            new EntityInventoryItemsConditionValidator(),
            new StatusConditionValidator(),
        ];

        return new ValidatorHandler($validators);
    }
}
