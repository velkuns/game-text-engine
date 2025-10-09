<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Item;

use Velkuns\GameTextEngine\Item\Traits\ItemConsumableTrait;
use Velkuns\GameTextEngine\Item\Traits\ItemEquipableTrait;
use Velkuns\GameTextEngine\Modifier\Modifier;

/**
 * @phpstan-import-type ModifierData from Modifier
 * @phpstan-type ItemData array{
 *     id: int,
 *     name: string,
 *     description: string,
 *     modifiers: array<ModifierData>,
 *     flag: int,
 *     isEquipped: bool
 * }
 */
class Item implements ItemInterface, ItemEquipableInterface, ItemConsumableInterface, \JsonSerializable
{
    use ItemConsumableTrait;
    use ItemEquipableTrait;

    /**
     * @throws \JsonException
     */
    public static function fromJson(string $jsonString): self
    {
        /** @var ItemData $data */
        $data = \json_decode($jsonString, associative: true, flags: \JSON_THROW_ON_ERROR);

        $modifiers = [];
        if ($data['modifiers'] !== []) {
            foreach ($data['modifiers'] as $modifierData) {
                $modifiers[] = new Modifier(
                    targetAbility: $modifierData['targetAbility'],
                    value: $modifierData['value'],
                    name: $modifierData['name'],
                    description: $modifierData['description'],
                );
            }
        }

        return new self(
            id: $data['id'],
            name: $data['name'],
            description: $data['description'],
            modifiers: $modifiers,
            flag: $data['flag'],
            isEquipped: $data['isEquipped'],
        );
    }

    /**
     * @param list<Modifier> $modifiers
     */
    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly string $description = '',
        private readonly array $modifiers = [],
        private readonly int $flag = 0,
        bool $isEquipped = false,
    ) {
        $this->isEquipped = $isEquipped;
    }

    public function getId(): int
    {
        return $this->id;
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
        return ($this->flag & $flag) === $flag;
    }

    /**
     * @return array{
     *      id: int,
     *      name: string,
     *      description: string,
     *      modifiers: array<Modifier>,
     *      flag: int,
     *      isEquipped: bool
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'modifiers'   => $this->modifiers,
            'flag'        => $this->flag,
            'isEquipped'  => $this->isEquipped,
        ];
    }
}
