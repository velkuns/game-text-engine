<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Graph\Factory;

use Velkuns\GameTextEngine\Element\Factory\ConditionsFactory;
use Velkuns\GameTextEngine\Graph\Edge;
use Velkuns\GameTextEngine\Graph\Graph;
use Velkuns\GameTextEngine\Graph\Node;

/**
 * @phpstan-import-type NodeData from Node
 * @phpstan-import-type EdgeData from Edge
 * @phpstan-import-type GraphData from Graph
 */
readonly class GraphFactory
{
    public function __construct(
        private ConditionsFactory $conditionsFactory,
    ) {}

    /**
     * @phpstan-param GraphData $data
     */
    public function graphFromData(array $data): Graph
    {
        $graph = new Graph($data['metadata']['title']);
        foreach ($data['nodes'] as $id => $nodeData) {
            $graph->addNode($this->nodeFromData($id, $nodeData));
        }

        foreach ($data['edges'] as $edgeData) {
            $graph->addEdge($this->edgeFromData($edgeData));
        }

        return $graph;
    }

    /**
     * @phpstan-param NodeData $data
     */
    public function nodeFromData(string $id, array $data): Node
    {
        return new Node($id, $data['metadata']['text'], $data['metadata']['trigger'] ?? null);
    }

    /**
     * @phpstan-param EdgeData $data
     */
    public function edgeFromData(array $data): Edge
    {
        return new Edge(
            $data['source'],
            $data['target'],
            $data['metadata']['text'],
            $this->conditionsFactory->from($data['metadata']['conditions'] ?? null),
        );
    }
}
