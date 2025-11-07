<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Api;

use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Item\ItemInterface;
use Velkuns\GameTextEngine\Exception\Api\GameApiException;
use Velkuns\GameTextEngine\Graph\Edge;
use Velkuns\GameTextEngine\Graph\Graph;
use Velkuns\GameTextEngine\Graph\Node;
use Velkuns\GameTextEngine\Rules\Abilities\AbilitiesRules;
use Velkuns\GameTextEngine\Rules\Combat\CombatRules;
use Velkuns\GameTextEngine\Rules\Player\PlayerRules;
use Velkuns\GameTextEngine\Rules\Statuses\StatusesRules;
use Velkuns\GameTextEngine\Utils\Exporter\DOTExporter;
use Velkuns\GameTextEngine\Utils\Loader\JsonLoader;
use Velkuns\GameTextEngine\Utils\Log\LootLog;
use Velkuns\GameTextEngine\Utils\Log\XpLog;

/**
 * @phpstan-import-type GraphData from Graph
 * @phpstan-import-type ItemData from ItemInterface
 * @phpstan-import-type BestiaryFileData from BestiaryApi
 * @phpstan-import-type EntityData from EntityInterface
 * @phpstan-import-type AbilitiesRulesData from AbilitiesRules
 * @phpstan-import-type StatusesRulesData from StatusesRules
 * @phpstan-import-type CombatRulesData from CombatRules
 * @phpstan-import-type PlayerRulesData from PlayerRules
 * @phpstan-import-type TurnLogData from CombatApi
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
     * @phpstan-param BestiaryFileData $bestiaryData
     * @phpstan-param AbilitiesRulesData $abilitiesRulesData
     * @phpstan-param StatusesRulesData $statusesRulesData
     * @phpstan-param CombatRulesData $combatRulesData
     * @phpstan-param PlayerRulesData $playerRulesData
     * @phpstan-param EntityData|null $playerData
     */
    public function load(
        array $storyData,
        array $itemsData,
        array $bestiaryData,
        array $abilitiesRulesData,
        array $statusesRulesData,
        array $combatRulesData,
        array $playerRulesData,
        ?array $playerData = null,
    ): self {
        $this->story->load($storyData);
        $this->items->load($itemsData);
        $this->bestiary->load($bestiaryData);
        $this->abilities->load($abilitiesRulesData);
        $this->statuses->load($statusesRulesData);
        $this->combat->load($combatRulesData);
        $this->player->load($playerRulesData, $playerData);

        return $this;
    }

    public function loadFromFiles(
        string $storyFile,
        string $itemsFile,
        string $bestiaryFile,
        string $abilitiesRulesFile,
        string $statusesRulesFile,
        string $combatRulesFile,
        string $playerRulesFile,
        ?string $playerFile = null,
    ): self {

        /** @var GraphData $storyData */
        $storyData = $this->loader->fromFile($storyFile);

        /** @var list<ItemData> $itemsData */
        $itemsData = $this->loader->fromFile($itemsFile);

        /** @var BestiaryFileData $bestiaryData */
        $bestiaryData = $this->loader->fromFile($bestiaryFile);

        /** @var AbilitiesRulesData $abilitiesRulesData */
        $abilitiesRulesData = $this->loader->fromFile($abilitiesRulesFile);

        /** @var StatusesRulesData $statusesRulesData */
        $statusesRulesData = $this->loader->fromFile($statusesRulesFile);

        /** @var CombatRulesData $combatRulesData */
        $combatRulesData = $this->loader->fromFile($combatRulesFile);

        /** @var PlayerRulesData $playerRulesData */
        $playerRulesData = $this->loader->fromFile($playerRulesFile);

        /** @var EntityData $playerData */
        $playerData = $playerFile !== null ? $this->loader->fromFile($playerFile) : null;

        $this->load(
            $storyData,
            $itemsData,
            $bestiaryData,
            $abilitiesRulesData,
            $statusesRulesData,
            $combatRulesData,
            $playerRulesData,
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
        string $combatRulesJson,
        string $playerRulesJson,
        ?string $playerJson = null,
    ): self {

        /** @var GraphData $storyData */
        $storyData = $this->loader->fromString($storyJson);

        /** @var list<ItemData> $itemsData */
        $itemsData = $this->loader->fromString($itemsJson);

        /** @var BestiaryFileData $bestiaryData */
        $bestiaryData = $this->loader->fromString($bestiaryJson);

        /** @var AbilitiesRulesData $abilitiesRulesData */
        $abilitiesRulesData = $this->loader->fromString($abilitiesRulesJson);

        /** @var StatusesRulesData $statusesRulesData */
        $statusesRulesData = $this->loader->fromString($statusesRulesJson);

        /** @var CombatRulesData $combatRulesData */
        $combatRulesData = $this->loader->fromString($combatRulesJson);

        /** @var PlayerRulesData $playerRulesData */
        $playerRulesData = $this->loader->fromString($playerRulesJson);

        /** @var EntityData $playerData */
        $playerData = $playerJson !== null ? $this->loader->fromString($playerJson) : null;

        $this->load(
            $storyData,
            $itemsData,
            $bestiaryData,
            $abilitiesRulesData,
            $statusesRulesData,
            $combatRulesData,
            $playerRulesData,
            $playerData,
        );

        return $this;
    }

    /**
     * @return array{
     *     0: Node,
     *     1: Edge[],
     *     2: array{combat: array<int, TurnLogData>, loot: list<LootLog>, xp: list<XpLog>},
     * }
     */
    public function read(string $source, string $target): array
    {
        $logs = ['combat' => [], 'loot' => [], 'xp' => []];

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
     *     abilitiesRules: string,
     *     statusesRules: string,
     *     combatRules: string,
     *     playerRules: string,
     *     playerData: string,
     * }
     */
    public function dump(bool $prettyPrint = false): array
    {
        try {
            return [
                'story'          => $this->story->dump($prettyPrint),
                'items'          => $this->items->dump($prettyPrint),
                'bestiary'       => $this->bestiary->dump($prettyPrint),
                'abilitiesRules' => $this->abilities->dump($prettyPrint),
                'statusesRules'  => $this->statuses->dump($prettyPrint),
                'combatRules'    => $this->combat->dump($prettyPrint),
                'playerRules'    => $this->player->dump($prettyPrint),
                'playerData'     => $this->player->dumpPlayer($prettyPrint),
            ];
            // @codeCoverageIgnoreStart
        } catch (\Throwable $exception) {
            throw new GameApiException('Cannot dump game data: ' . $exception->getMessage(), 1500, $exception); // @codeCoverageIgnore
        }
        // @codeCoverageIgnoreEnd
    }
}
