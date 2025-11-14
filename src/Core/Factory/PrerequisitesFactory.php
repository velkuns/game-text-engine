<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Factory;

use Velkuns\GameTextEngine\Core\Prerequisite\Requirement;
use Velkuns\GameTextEngine\Core\Prerequisite\RequirementInterface;
use Velkuns\GameTextEngine\Core\Prerequisite\ConditionParser;
use Velkuns\GameTextEngine\Core\Prerequisite\Prerequisites;
use Velkuns\GameTextEngine\Core\Resolver\TypeResolverHandler;
use Velkuns\GameTextEngine\Core\Validator\ValidatorHandler;

/**
 * @phpstan-import-type PrerequisitesData from Prerequisites
 * @phpstan-import-type RequirementData from RequirementInterface
 */
readonly class PrerequisitesFactory
{
    public function __construct(
        private ConditionParser $parser,
        private TypeResolverHandler $resolverHandler,
        private ValidatorHandler $validatorHandler,
    ) {}

    /**
     * @phpstan-param PrerequisitesData $data
     */
    public function from(?array $data): ?Prerequisites
    {
        if ($data === null) {
            return null;
        }

        $numberRequired = $data['numberRequired'];
        $requirements   = \array_map(fn($requirement) => $this->fromRequirement($requirement), $data['requirements']);

        return new Prerequisites($numberRequired, $requirements);
    }

    /**
     * @phpstan-param RequirementData $data
     */
    private function fromRequirement(array $data): Requirement
    {
        $type      = $data['type'];
        $condition = $data['condition'];
        $is        = $data['is'];

        return new Requirement($this->parser, $this->resolverHandler, $this->validatorHandler, $type, $condition, $is);
    }
}
