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
use Velkuns\GameTextEngine\Exception\Api\StoryApiException;
use Velkuns\GameTextEngine\Graph\Edge;
use Velkuns\GameTextEngine\Graph\Factory\GraphFactory;
use Velkuns\GameTextEngine\Graph\Graph;
use Velkuns\GameTextEngine\Graph\Node;

/**
 * @phpstan-import-type GraphData from Graph
 */
class StoryApi
{
    public Graph $graph;

    public function __construct(
        private readonly GraphFactory $graphFactory,
    ) {}

    /**
     * @phpstan-param GraphData $data
     */
    public function load(array $data): void
    {
        $this->graph = $this->graphFactory->graphFromData($data);
    }

    public function start(): Node
    {
        return $this->graph->getNode('1');
    }

    public function goto(
        string $source,
        string $target,
        EntityInterface $player,
        ?EntityInterface $enemy = null,
    ): Node {
        if (!$this->validate($source, $target, $player, $enemy)) {
            throw new StoryApiException("Cannot go from node '$source' to node '$target'.", 1400);
        }

        return $this->getText($target);

    }

    public function validate(
        string $source,
        string $target,
        EntityInterface $player,
        ?EntityInterface $enemy = null,
    ): bool {
        if ($source === $target) {
            return true;
        }

        $edges = $this->graph->getEdgesFromSource($source);

        foreach ($edges as $edge) {
            if ($edge->target === $target && $edge->available($player, $enemy)) {
                return true;
            }
        }
        return false;
    }

    public function getText(string $id): Node
    {
        return $this->graph->getNode($id);
    }

    /**
     * @return list<Edge>
     */
    public function getPossibleChoices(string $id, EntityInterface $player, ?EntityInterface $enemy = null): array
    {
        $edges = $this->graph->getEdgesFromSource($id);

        return \array_values(\array_filter($edges, fn(Edge $edge) => $edge->available($player, $enemy)));
    }

    public function dump(bool $prettyPrint = false): string
    {
        try {
            return \json_encode(
                $this->graph->jsonSerialize(),
                flags: \JSON_THROW_ON_ERROR | ($prettyPrint ? \JSON_PRETTY_PRINT : 0),
            );
            // @codeCoverageIgnoreStart
        } catch (\JsonException) {
            throw new StoryApiException('Unable to dump story graph to JSON.', 1401); // @codeCoverageIgnore
        }
        // @codeCoverageIgnoreEnd
    }
}
