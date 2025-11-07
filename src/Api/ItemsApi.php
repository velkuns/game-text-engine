<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Velkuns\GameTextEngine\Api;

use Velkuns\GameTextEngine\Api\Exception\ItemException;
use Velkuns\GameTextEngine\Element\Factory\ItemFactory;
use Velkuns\GameTextEngine\Element\Item\ItemInterface;

/**
 * @phpstan-import-type ItemData from ItemInterface
 */
class ItemsApi
{
    /** @var array<string, ItemInterface> $items */
    private array $items = [];

    public function __construct(
        private readonly ItemFactory $factory,
    ) {}

    /**
     * @phpstan-param list<ItemData> $list
     */
    public function load(array $list): void
    {
        $this->items = [];
        foreach ($list as $data) {
            $item   = $this->factory->from($data);
            $this->items[\strtolower($item->getName())] = $item;
        }
    }

    public function get(string $name, bool $asClone = true): ItemInterface
    {
        $lowerCaseName = \strtolower($name);
        if (!isset($this->items[$lowerCaseName])) {
            throw new ItemException("Item '$name' not found in item list.", 1601);
        }

        return $asClone ? $this->items[$lowerCaseName]->clone() : $this->items[$lowerCaseName];
    }

    public function set(ItemInterface $item): self
    {
        $this->items[\strtolower($item->getName())] = $item;

        return $this;
    }

    public function remove(string $name): self
    {
        $lowerCaseName = \strtolower($name);
        if (isset($this->items[$lowerCaseName])) {
            unset($this->items[$lowerCaseName]);
        }

        return $this;
    }

    public function dump(bool $prettyPrint = false): string
    {
        try {
            return \json_encode(
                \array_values($this->items),
                flags: \JSON_THROW_ON_ERROR | ($prettyPrint ? \JSON_PRETTY_PRINT : 0),
            );
            // @codeCoverageIgnoreStart
        } catch (\JsonException $exception) {
            throw new ItemException("Unable to dump items data: " . $exception->getMessage(), 1600, $exception); // @codeCoverageIgnore
        }
        // @codeCoverageIgnoreEnd
    }
}
