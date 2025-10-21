<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Velkuns\GameTextEngine\Api;

use Velkuns\GameTextEngine\Api\Exception\GameException;
use Velkuns\GameTextEngine\Api\Loader\JsonLoader;
use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Item\ItemInterface;
use Velkuns\GameTextEngine\Graph\Graph;

/**
 * @phpstan-import-type GraphData from Graph
 * @phpstan-import-type ItemData from ItemInterface
 * @phpstan-import-type BestiaryData from Bestiary
 * @phpstan-import-type EntityData from EntityInterface
 */
class GameApi
{
    public function __construct(
        public readonly JsonLoader $loader,
        public readonly Story $story,
        public readonly Items $items,
        public readonly Bestiary $bestiary,
        public readonly Combat $combat,
    ) {}

    /**
     * @phpstan-param GraphData $storyData
     * @phpstan-param list<ItemData> $itemsData
     * @phpstan-param list<BestiaryData> $bestiaryData
     */
    public function load(array $storyData, array $itemsData, array $bestiaryData): self
    {
        $this->story->load($storyData);
        $this->items->load($itemsData);
        $this->bestiary->load($bestiaryData);

        return $this;
    }

    /**
     * @phpstan-return array{
     *     story: string,
     *     items: string,
     *     bestiary: string,
     * }
     */
    public function dump(bool $prettyPrint = false): array
    {
        try {
            return [
                'story' => $this->story->dump($prettyPrint),
                'items' => $this->items->dump($prettyPrint),
                'bestiary' => $this->bestiary->dump($prettyPrint),
            ];
            // @codeCoverageIgnoreStart
        } catch (\Throwable $exception) {
            throw new GameException('Cannot dump game data: ' . $exception->getMessage(), 1500, $exception); // @codeCoverageIgnore
        }
        // @codeCoverageIgnoreEnd
    }
}
