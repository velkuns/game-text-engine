<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Item;

use Velkuns\GameTextEngine\Element\Modifier\Modifier;

/**
 * @phpstan-import-type ItemData from ItemInterface
 */
readonly class Item implements ItemInterface
{
    /**
     * @param list<Modifier> $modifiers
     */
    public function __construct(
        private string $name,
        private ?string $subType = null,
        private string $description = '',
        private array $modifiers = [],
        private int $flags = 0,
        private bool $equipped = false,
        private int $damages = 0,
        private int $price = 0,
        private string $type = 'item',
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

    public function getSubType(): ?string
    {
        return $this->subType;
    }

    public function getDamages(): int
    {
        return $this->damages;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function equipped(): bool
    {
        return $this->equipped;
    }

    public function isConsumable(): bool
    {
        return $this->isItemType(ItemFlag::CONSUMABLE);
    }

    public function isEquipable(): bool
    {
        return $this->isItemType(ItemFlag::EQUIPABLE);
    }

    public function isWeapon(): bool
    {
        return $this->isItemType(ItemFlag::WEAPON);
    }

    public function isGear(): bool
    {
        return $this->isItemType(ItemFlag::GEAR);
    }

    private function isItemType(int $flag): bool
    {
        return ($this->flags & $flag) === $flag;
    }

    /**
     * @return ItemData
     */
    public function jsonSerialize(): array
    {
        return [
            'type'        => $this->type,
            'name'        => $this->name,
            'subType'     => $this->subType,
            'description' => $this->description,
            'modifiers'   => \array_map(fn(Modifier $modifier) => $modifier->jsonSerialize(), $this->modifiers),
            'flags'       => $this->flags,
            'equipped'    => $this->equipped,
            'damages'     => $this->damages,
            'price'       => $this->price,
        ];
    }

    public function clone(): self
    {
        return new self(
            $this->name,
            $this->subType,
            $this->description,
            \array_map(
                fn(Modifier $modifier): Modifier => $modifier->clone(),
                $this->modifiers,
            ),
            $this->flags,
            $this->equipped,
            $this->damages,
            $this->price,
            $this->type,
        );
    }
}
