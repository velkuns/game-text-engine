<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rpg\Trait;

use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;

/**
 * @phpstan-import-type TraitData from TraitInterface
 */
class EntityTrait implements TraitInterface
{
    /**
     * @param list<Modifier> $modifiers
     */
    public function __construct(
        private readonly string $type,
        private readonly string $name,
        private readonly string $description,
        private readonly array $modifiers,
    ) {}

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return list<Modifier>
     */
    public function getModifiers(): array
    {
        return $this->modifiers;
    }

    /**
     * @return TraitData
     */
    public function jsonSerialize(): array
    {
        return [
            'type'           => $this->getType(),
            'name'           => $this->getName(),
            'description'    => $this->getDescription(),
            'modifiers'      => array_map(fn(Modifier $modifier) => $modifier->jsonSerialize(), $this->getModifiers()),
        ];
    }

    public function clone(): self
    {
        return new self(
            $this->type,
            $this->name,
            $this->description,
            modifiers: array_map(
                fn(Modifier $modifier): Modifier => $modifier->clone(),
                $this->modifiers,
            ),
        );
    }
}
