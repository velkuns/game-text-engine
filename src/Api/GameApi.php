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
use Velkuns\GameTextEngine\Graph\Edge;
use Velkuns\GameTextEngine\Graph\Graph;
use Velkuns\GameTextEngine\Graph\Node;
use Velkuns\GameTextEngine\Utils\Exporter\DOTExporter;
use Velkuns\GameTextEngine\Utils\Loader\JsonLoader;
use Velkuns\GameTextEngine\Utils\Log\CombatLog;

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
        public StoryApi $story,
        public ItemsApi $items,
        public BestiaryApi $bestiary,
        public AbilitiesApi $abilities,
        public StatusesApi $statuses,
        public PlayerApi $player,
        public CombatApi $combat,
    ) {}

    /**
     * @phpstan-param GraphData $storyData
     * @phpstan-param list<ItemData> $itemsData
     * @phpstan-param list<BestiaryData> $bestiaryData
     * @phpstan-param AbilitiesRulesData $abilitiesRulesData
     * @phpstan-param StatusesRulesData $statusesRulesData
     * @phpstan-param EntityData|null $playerData
     */
    public function load(
        array $storyData,
        array $itemsData,
        array $bestiaryData,
        array $abilitiesRulesData,
        array $statusesRulesData,
        ?array $playerData = null,
    ): self {
        $this->story->load($storyData);
        $this->items->load($itemsData);
        $this->bestiary->load($bestiaryData);
        $this->abilities->load($abilitiesRulesData);
        $this->statuses->load($statusesRulesData);

        if ($playerData !== null) {
            $this->player->load($playerData);
        }

        return $this;
    }

    public function loadFromFiles(
        string $storyFile,
        string $itemsFile,
        string $bestiaryFile,
        string $abilitiesRulesFile,
        string $statusesRulesFile,
        ?string $playerFile = null,
    ): self {

        /** @var GraphData $storyData */
        $storyData = $this->loader->fromFile($storyFile);

        /** @var list<ItemData> $itemsData */
        $itemsData = $this->loader->fromFile($itemsFile);

        /** @var list<BestiaryData> $bestiaryData */
        $bestiaryData = $this->loader->fromFile($bestiaryFile);

        /** @var AbilitiesRulesData $abilitiesRulesData */
        $abilitiesRulesData = $this->loader->fromFile($abilitiesRulesFile);

        /** @var StatusesRulesData $statusesRulesData */
        $statusesRulesData = $this->loader->fromFile($statusesRulesFile);

        /** @var EntityData $playerData */
        $playerData = $playerFile !== null ? $this->loader->fromFile($playerFile) : null;

        $this->load(
            $storyData,
            $itemsData,
            $bestiaryData,
            $abilitiesRulesData,
            $statusesRulesData,
            $playerData,
        );

        return $this;
    }

    public function loadFromJsons(
        string $storyJson,
        string $itemsJson,
        string $bestiaryJson,
        string $abilitiesRulesJson,
        string $statusesRulesJson,
        ?string $playerJson = null,
    ): self {

        /** @var GraphData $storyData */
        $storyData = $this->loader->fromString($storyJson);

        /** @var list<ItemData> $itemsData */
        $itemsData = $this->loader->fromString($itemsJson);

        /** @var list<BestiaryData> $bestiaryData */
        $bestiaryData = $this->loader->fromString($bestiaryJson);

        /** @var AbilitiesRulesData $abilitiesRulesData */
        $abilitiesRulesData = $this->loader->fromString($abilitiesRulesJson);

        /** @var StatusesRulesData $statusesRulesData */
        $statusesRulesData = $this->loader->fromString($statusesRulesJson);

        /** @var EntityData $playerData */
        $playerData = $playerJson !== null ? $this->loader->fromString($playerJson) : null;

        $this->load(
            $storyData,
            $itemsData,
            $bestiaryData,
            $abilitiesRulesData,
            $statusesRulesData,
            $playerData,
        );

        return $this;
    }

    /**
     * @return array{0: Node, 1: Edge[], 2: array<int, array{player: CombatLog, enemy?: CombatLog}>}
     */
    public function read(string $source, string $target): array
    {
        $logs = [];

        //~ Validate the path
        $node = $this->story->goto($source, $target, $this->player->player);

        //~ Handle trigger if necessary
        if ($source !== $target && $node->trigger !== null && isset($node->trigger['combat'])) {
            $enemies = [];
            foreach ($node->trigger['combat']['enemies'] as $name) {
                $enemies[] = $this->bestiary->get($name);
            }
            $logs = $this->combat->auto($this->player->player, $enemies);
        }

        //~ Get choices
        $choices = $this->story->getPossibleChoices($target, $this->player->player);

        return [$node, $choices, $logs];
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
                'story'     => $this->story->dump($prettyPrint),
                'items'     => $this->items->dump($prettyPrint),
                'bestiary'  => $this->bestiary->dump($prettyPrint),
                'abilities' => $this->abilities->dump($prettyPrint),
                'statuses'  => $this->statuses->dump($prettyPrint),
                'player'    => $this->player->dump($prettyPrint),
            ];
            // @codeCoverageIgnoreStart
        } catch (\Throwable $exception) {
            throw new GameException('Cannot dump game data: ' . $exception->getMessage(), 1500, $exception); // @codeCoverageIgnore
        }
        // @codeCoverageIgnoreEnd
    }
}
