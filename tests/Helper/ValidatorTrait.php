<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Helper;

use Velkuns\GameTextEngine\Core\Validator\AttributeConditionValidator;
use Velkuns\GameTextEngine\Core\Validator\EntityInfoConditionValidator;
use Velkuns\GameTextEngine\Core\Validator\EntityInventoryItemsConditionValidator;
use Velkuns\GameTextEngine\Core\Validator\StatusConditionValidator;
use Velkuns\GameTextEngine\Core\Validator\ValidatorHandler;

trait ValidatorTrait
{
    private static function getValidatorHandler(): ValidatorHandler
    {
        $validators = [
            new AttributeConditionValidator(),
            new EntityInfoConditionValidator(),
            new EntityInventoryItemsConditionValidator(),
            new StatusConditionValidator(),
        ];

        return new ValidatorHandler($validators);
    }
}
