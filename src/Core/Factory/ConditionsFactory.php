<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Factory;

use Velkuns\GameTextEngine\Core\Condition\Condition;
use Velkuns\GameTextEngine\Core\Condition\ConditionInterface;
use Velkuns\GameTextEngine\Core\Condition\ConditionParser;
use Velkuns\GameTextEngine\Core\Condition\Conditions;
use Velkuns\GameTextEngine\Core\Resolver\TypeElementResolverHandler;
use Velkuns\GameTextEngine\Core\Validator\ValidatorHandler;

/**
 * @phpstan-import-type ConditionsData from Conditions
 * @phpstan-import-type ConditionData from ConditionInterface
 */
readonly class ConditionsFactory
{
    public function __construct(
        private ConditionParser $parser,
        private TypeElementResolverHandler $resolverHandler,
        private ValidatorHandler $validatorHandler,
    ) {}

    /**
     * @phpstan-param ConditionsData $data
     */
    public function from(?array $data): ?Conditions
    {
        if ($data === null) {
            return null;
        }

        $numberRequired = $data['numberRequired'];
        $conditions     = \array_map(fn($condition) => $this->fromCondition($condition), $data['conditions']);

        return new Conditions($numberRequired, $conditions);
    }

    /**
     * @phpstan-param ConditionData $data
     */
    private function fromCondition(array $data): Condition
    {
        $type      = $data['type'];
        $condition = $data['condition'];
        $is        = $data['is'];

        return new Condition($this->parser, $this->resolverHandler, $this->validatorHandler, $type, $condition, $is);
    }
}
