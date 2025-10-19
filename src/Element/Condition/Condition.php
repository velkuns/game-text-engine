<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Condition;

use Velkuns\GameTextEngine\Element\Entity\EntityInterface;

/**
 * @phpstan-import-type ConditionData from ConditionInterface
 */
readonly class Condition implements ConditionInterface
{
    public function __construct(
        public ConditionParser $parser,
        public ConditionElementResolver $resolver,
        public ConditionValidatorInterface $validator,
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
        $elements   = $this->resolver->resolve($entity, $this);
        $conditions = $this->parser->parse($this->condition);

        $evaluation = false;

        foreach ($elements as $element) {
            if ($this->validator->validate($element, $conditions)) {
                $evaluation = true;
                break;
            }
        }

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
}
