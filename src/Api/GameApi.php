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
use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Item\ItemInterface;
use Velkuns\GameTextEngine\Graph\Graph;
use Velkuns\GameTextEngine\Utils\Exporter\DOTExporter;
use Velkuns\GameTextEngine\Utils\Loader\JsonLoader;

/**
 * @phpstan-import-type GraphData from Graph
 * @phpstan-import-type ItemData from ItemInterface
 * @phpstan-import-type BestiaryData from BestiaryApi
 * @phpstan-import-type EntityData from EntityInterface
 * @phpstan-import-type AbilitiesRulesData from AbilitiesApi
 * @phpstan-import-type StatusesRulesData from StatusesApi
 */
readonly class GameApi
{
    public function __construct(
        public JsonLoader $loader,
        public DOTExporter $exporter,
        public StoryApi $storyApi,
        public ItemsApi $itemsApi,
        public BestiaryApi $bestiaryApi,
        public AbilitiesApi $abilitiesApi,
        public StatusesApi $statusesApi,
        public PlayerApi $playerApi,
        public CombatApi $combatApi,
    ) {}

    /**
     * @phpstan-param GraphData $storyData
     * @phpstan-param list<ItemData> $itemsData
     * @phpstan-param list<BestiaryData> $bestiaryData
     * @phpstan-param AbilitiesRulesData $abilitiesRulesData
     * @phpstan-param StatusesRulesData $statusesRulesData
     * @phpstan-param EntityData $playerData
     */
    public function load(
        array $storyData,
        array $itemsData,
        array $bestiaryData,
        array $abilitiesRulesData,
        array $statusesRulesData,
        array $playerData,
    ): self {
        $this->storyApi->load($storyData);
        $this->itemsApi->load($itemsData);
        $this->bestiaryApi->load($bestiaryData);
        $this->abilitiesApi->load($abilitiesRulesData);
        $this->statusesApi->load($statusesRulesData);
        $this->playerApi->load($playerData);

        return $this;
    }

    /**
     * @phpstan-return array{
     *     story: string,
     *     items: string,
     *     bestiary: string,
     *     abilities: string,
     *     statuses: string,
     *     player: string,
     * }
     */
    public function dump(bool $prettyPrint = false): array
    {
        try {
            return [
                'story'     => $this->storyApi->dump($prettyPrint),
                'items'     => $this->itemsApi->dump($prettyPrint),
                'bestiary'  => $this->bestiaryApi->dump($prettyPrint),
                'abilities' => $this->abilitiesApi->dump($prettyPrint),
                'statuses'  => $this->statusesApi->dump($prettyPrint),
                'player'    => $this->playerApi->dump($prettyPrint),
            ];
            // @codeCoverageIgnoreStart
        } catch (\Throwable $exception) {
            throw new GameException('Cannot dump game data: ' . $exception->getMessage(), 1500, $exception); // @codeCoverageIgnore
        }
        // @codeCoverageIgnoreEnd
    }
}
