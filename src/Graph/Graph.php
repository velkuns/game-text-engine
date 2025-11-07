<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Graph;

use Velkuns\GameTextEngine\Exception\Graph\GraphException;

/**
 * @phpstan-import-type NodeData from Node
 * @phpstan-import-type EdgeData from Edge
 * @phpstan-type GraphData array{metadata: array{title: string}, nodes: NodeData[], edges: list<EdgeData>}
 */
class Graph implements \JsonSerializable
{
    /** @var array<string, Edge[]> */
    private array $edgesFromSource = [];

    /** @var array<string, Edge[]> */
    private array $edgesToTarget = [];

    /**
     * @param string $title
     * @param Node[] $nodes
     * @param Edge[] $edges
     */
    public function __construct(
        public readonly string $title,
        private array $nodes = [],
        private array $edges = [],
    ) {}

    public function getNode(string $id): Node
    {
        if (!isset($this->nodes[$id])) {
            throw new GraphException("Node with id '$id' does not exist in the graph.", 1302);
        }

        return $this->nodes[$id];
    }

    /**
     * @return Edge[]
     */
    public function getEdgesFromSource(string $source): array
    {
        return $this->edgesFromSource[$source] ?? [];
    }

    /**
     * @return Node[]
     */
    public function getAllNodes(): array
    {
        return $this->nodes;
    }

    /**
     * @return Edge[]
     */
    public function getAllEdges(): array
    {
        return $this->edges;
    }

    public function addNode(Node $node): self
    {
        if (isset($this->nodes[$node->id])) {
            throw new GraphException("Node with id '$node->id' already exists in the graph.", 1300);
        }
        $this->nodes[$node->id] = $node;

        return $this;
    }

    public function addEdge(Edge $edge): self
    {
        if (!isset($this->nodes[$edge->source]) || !isset($this->nodes[$edge->target])) {
            throw new GraphException('Both nodes must exist in the graph before adding an edge.', 1301);
        }

        $this->edges[$edge->source . '.' . $edge->target]    = $edge;
        $this->edgesFromSource[$edge->source][$edge->target] = $edge;
        $this->edgesToTarget[$edge->target][$edge->source]   = $edge;

        return $this;
    }

    public function removeNode(string $id): self
    {
        if (!isset($this->nodes[$id])) {
            throw new GraphException("The node '$id' does not exists in the graph.", 1303);
        }

        //~ Remove all edge from this node source if exist
        foreach ($this->edgesFromSource[$id] ?? [] as $edge) {
            $this->removeEdge($edge);
        }

        //~ Remove the edges to this source if exist
        foreach ($this->edgesToTarget[$id] ?? [] as $edge) {
            $this->removeEdge($edge);
        }

        //~ Then remove node
        unset($this->nodes[$id]);

        return $this;
    }

    public function removeEdgeBetweenNodes(string $source, string $target): self
    {
        if (!isset($this->edges[$source . '.' . $target])) {
            throw new GraphException("The edge between nodes '$source' and '$target' does not exist.", 1304);
        }
        return $this->removeEdge($this->edges[$source . '.' . $target]);
    }

    private function removeEdge(Edge $edge): self
    {
        unset($this->edgesToTarget[$edge->target][$edge->source]);
        unset($this->edgesFromSource[$edge->source][$edge->target]);
        unset($this->edges[$edge->source . '.' . $edge->target]);

        return $this;
    }

    /**
     * @phpstan-return GraphData
     */
    public function jsonSerialize(): array
    {
        return [
            'metadata' => ['title' => $this->title],
            'nodes'    => \array_map(fn(Node $node) => $node->jsonSerialize(), $this->nodes),
            'edges'    => \array_values(\array_map(fn(Edge $edge) => $edge->jsonSerialize(), $this->edges)),
        ];
    }
}
