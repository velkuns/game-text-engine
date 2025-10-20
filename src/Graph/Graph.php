<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Graph;

use Velkuns\GameTextEngine\Graph\Exception\GraphException;

/**
 * @phpstan-import-type NodeData from Node
 * @phpstan-import-type EdgeData from Edge
 * @phpstan-type GraphData array{metadata: array{title: string}, nodes: array<string, NodeData>, edges: list<EdgeData>}
 */
class Graph implements \JsonSerializable
{
    /**
     * @param string $title
     * @param array<string, Node> $nodes
     * @param list<Edge> $edges
     * @param array<string, list<Edge>> $choices
     */
    public function __construct(
        public readonly string $title,
        private array $nodes = [],
        private array $edges = [],
        private array $choices = [],
    ) {}

    public function getNode(string $id): Node
    {
        if (!isset($this->nodes[$id])) {
            throw new GraphException("Node with id '$id' does not exist in the graph.", 1302);
        }

        return $this->nodes[$id];
    }

    /**
     * @return list<Edge>
     */
    public function getEdges(string $source): array
    {
        return $this->choices[$source] ?? [];
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

        $this->edges[]                  = $edge;
        $this->choices[$edge->source][] = $edge;

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
            'edges'    => \array_map(fn(Edge $edge) => $edge->jsonSerialize(), $this->edges),
        ];
    }
}
