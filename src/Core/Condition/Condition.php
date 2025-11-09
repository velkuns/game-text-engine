<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Condition;

use Velkuns\GameTextEngine\Core\Resolver\TypeResolverHandler;
use Velkuns\GameTextEngine\Core\Validator\ValidatorHandler;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;

/**
 * @phpstan-import-type ConditionData from ConditionInterface
 */
readonly class Condition implements ConditionInterface
{
    public function __construct(
        public ConditionParser $parser,
        public TypeResolverHandler $resolverHandler,
        public ValidatorHandler $validatorHandler,
        public string $type,
        public string $condition,
        public bool $is,
    ) {}

    public function getType(): string
    {
        return $this->type;
    }

    public function getCondition(): string
    {
        return $this->condition;
    }

    public function is(): bool
    {
        return $this->is;
    }

    public function evaluate(EntityInterface $entity): bool
    {
        $element    = $this->resolverHandler->handle($this->getType(), $entity);
        $conditions = $this->parser->parse($this->condition);

        $evaluation = $this->validatorHandler->handle($this->getType(), $element, $conditions);

        return $this->is() === $evaluation;

    }

    /**
     * @return ConditionData
     */
    public function jsonSerialize(): array
    {
        return [
            'type'      => $this->type,
            'condition' => $this->condition,
            'is'        => $this->is,
        ];
    }

    public function clone(): self
    {
        return new self(
            $this->parser,
            $this->resolverHandler,
            $this->validatorHandler,
            $this->type,
            $this->condition,
            $this->is,
        );
    }
}
