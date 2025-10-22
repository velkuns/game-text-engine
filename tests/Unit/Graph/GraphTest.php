<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Graph;

use Velkuns\GameTextEngine\Api\Loader\JsonLoader;
use Velkuns\GameTextEngine\Graph\Edge;
use Velkuns\GameTextEngine\Graph\Exception\GraphException;
use Velkuns\GameTextEngine\Graph\Graph;
use Velkuns\GameTextEngine\Graph\Node;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;
use PHPUnit\Framework\TestCase;

/**
 * @phpstan-import-type GraphData from Graph
 */
class GraphTest extends TestCase
{
    use FactoryTrait;

    public function testGraph(): void
    {
        $graph = new Graph('Test Graph');

        $trigger = ['combat' => ['enemies' => ['Goblin']]];

        $node1 = new Node('text_1', 'Text for node text_1.');
        $node2 = new Node('text_2', 'Text for node text_2.', $trigger);
        $edge  = new Edge('text_1', 'text_2', 'Continue to text 2');
        $graph->addNode($node1);
        $graph->addNode($node2);
        $graph->addEdge($edge);


        self::assertSame($node1, $graph->getNode('text_1'));
        self::assertSame($node2, $graph->getNode('text_2'));
        self::assertSame(['text_2' => $edge], $graph->getEdgesFromSource('text_1'));

        $data = [
            'metadata' => [
                'title' => 'Test Graph',
            ],
            'nodes' => [
                'text_1' => [
                    'metadata' => [
                        'text' => 'Text for node text_1.',
                    ],
                ],
                'text_2' => [
                    'metadata' => [
                        'text' => 'Text for node text_2.',
                        'trigger' => [
                            'combat' => [
                                'enemies' => ['Goblin'],
                            ],
                        ],
                    ],
                ],
            ],
            'edges' => [
                [
                    'source' => 'text_1',
                    'target' => 'text_2',
                    'label'  => 'action',
                    'metadata' => [
                        'text'       => 'Continue to text 2',
                    ],
                ],
            ],
        ];

        self::assertSame($data, $graph->jsonSerialize());
    }

    public function testRemoveNode(): void
    {
        $graph = new Graph('Test Graph');

        $node1 = new Node('text_1', 'Start');
        $node2 = new Node('text_2', '2 -> 3, 2 -> 5');
        $node3 = new Node('text_3', '3 -> 4');
        $node4 = new Node('text_4', '4 -> Death, 4 -> End');
        $node5 = new Node('text_5', '5 -> End');
        $node8 = new Node('text_8', 'Death');
        $node9 = new Node('text_9', 'End');

        $edge1_2 = new Edge('text_1', 'text_2', 'Continue to 2');
        $edge2_3 = new Edge('text_2', 'text_3', 'Continue to 3');
        $edge3_4 = new Edge('text_3', 'text_4', 'Continue to 4');
        $edge4_8 = new Edge('text_4', 'text_8', 'Continue to death');
        $edge4_9 = new Edge('text_4', 'text_9', 'Continue to end');
        $edge2_5 = new Edge('text_2', 'text_5', 'Continue to 5');
        $edge5_9 = new Edge('text_2', 'text_9', 'Continue to end');

        //~ Add all nodes
        $graph->addNode($node1);
        $graph->addNode($node2);
        $graph->addNode($node3);
        $graph->addNode($node4);
        $graph->addNode($node5);
        $graph->addNode($node8);
        $graph->addNode($node9);

        //~ Add all edges
        $graph->addEdge($edge1_2);
        $graph->addEdge($edge2_3);
        $graph->addEdge($edge3_4);
        $graph->addEdge($edge4_8);
        $graph->addEdge($edge4_9);
        $graph->addEdge($edge2_5);
        $graph->addEdge($edge5_9);

        $expectedBefore = [
            'metadata' => ['title' => 'Test Graph',],
            'nodes' => [
                'text_1' => ['metadata' => ['text' => 'Start']],
                'text_2' => ['metadata' => ['text' => '2 -> 3, 2 -> 5']],
                'text_3' => ['metadata' => ['text' => '3 -> 4']],
                'text_4' => ['metadata' => ['text' => '4 -> Death, 4 -> End']],
                'text_5' => ['metadata' => ['text' => '5 -> End']],
                'text_8' => ['metadata' => ['text' => 'Death']],
                'text_9' => ['metadata' => ['text' => 'End']],
            ],
            'edges' => [
                ['source' => 'text_1', 'target' => 'text_2', 'label'  => 'action', 'metadata' => ['text' => 'Continue to 2']],
                ['source' => 'text_2', 'target' => 'text_3', 'label'  => 'action', 'metadata' => ['text' => 'Continue to 3']],
                ['source' => 'text_3', 'target' => 'text_4', 'label'  => 'action', 'metadata' => ['text' => 'Continue to 4']],
                ['source' => 'text_4', 'target' => 'text_8', 'label'  => 'action', 'metadata' => ['text' => 'Continue to death']],
                ['source' => 'text_4', 'target' => 'text_9', 'label'  => 'action', 'metadata' => ['text' => 'Continue to end']],
                ['source' => 'text_2', 'target' => 'text_5', 'label'  => 'action', 'metadata' => ['text' => 'Continue to 5']],
                ['source' => 'text_2', 'target' => 'text_9', 'label'  => 'action', 'metadata' => ['text' => 'Continue to end']],
            ],
        ];

        $expectedAfterRemoveNode = [
            'metadata' => ['title' => 'Test Graph',],
            'nodes' => [
                'text_1' => ['metadata' => ['text' => 'Start']],
                'text_2' => ['metadata' => ['text' => '2 -> 3, 2 -> 5']],
                'text_3' => ['metadata' => ['text' => '3 -> 4']],
                'text_5' => ['metadata' => ['text' => '5 -> End']],
                'text_8' => ['metadata' => ['text' => 'Death']],
                'text_9' => ['metadata' => ['text' => 'End']],
            ],
            'edges' => [
                ['source' => 'text_1', 'target' => 'text_2', 'label'  => 'action', 'metadata' => ['text' => 'Continue to 2']],
                ['source' => 'text_2', 'target' => 'text_3', 'label'  => 'action', 'metadata' => ['text' => 'Continue to 3']],
                ['source' => 'text_2', 'target' => 'text_5', 'label'  => 'action', 'metadata' => ['text' => 'Continue to 5']],
                ['source' => 'text_2', 'target' => 'text_9', 'label'  => 'action', 'metadata' => ['text' => 'Continue to end']],
            ],
        ];

        //~ Remove node text_4. That will remove edges FROM and TO this node
        $expectedAfterRemoveEdge = [
            'metadata' => ['title' => 'Test Graph',],
            'nodes' => [
                'text_1' => ['metadata' => ['text' => 'Start']],
                'text_2' => ['metadata' => ['text' => '2 -> 3, 2 -> 5']],
                'text_3' => ['metadata' => ['text' => '3 -> 4']],
                'text_5' => ['metadata' => ['text' => '5 -> End']],
                'text_8' => ['metadata' => ['text' => 'Death']],
                'text_9' => ['metadata' => ['text' => 'End']],
            ],
            'edges' => [
                ['source' => 'text_1', 'target' => 'text_2', 'label'  => 'action', 'metadata' => ['text' => 'Continue to 2']],
                ['source' => 'text_2', 'target' => 'text_5', 'label'  => 'action', 'metadata' => ['text' => 'Continue to 5']],
                ['source' => 'text_2', 'target' => 'text_9', 'label'  => 'action', 'metadata' => ['text' => 'Continue to end']],
            ],
        ];

        //~ Assert graph is ok
        self::assertSame($expectedBefore, $graph->jsonSerialize());


        //~ Remove node text_4. That will remove edges FROM and TO this node
        $graph->removeNode('text_4');
        self::assertSame($expectedAfterRemoveNode, $graph->jsonSerialize());

        //~ Assert graph is ok after the remove of single edge between two node
        $graph->removeEdgeBetweenNodes('text_2', 'text_3');
        self::assertSame($expectedAfterRemoveEdge, $graph->jsonSerialize());
    }

    public function testGraphWhenTryToAddEdgeButNodeDoesNotExist(): void
    {
        $graph = new Graph('Test Graph');

        self::expectException(GraphException::class);
        self::expectExceptionCode(1302);
        $graph->getNode('text_1');
    }

    public function testGraphWhenAddNodeButNodeWithSameIdAlreadyExists(): void
    {
        $graph = new Graph('Test Graph');
        $node1 = new Node('text_1', 'Text for node text_1.');
        $node2 = new Node('text_1', 'Text for node text_2.');
        $graph->addNode($node1);

        self::expectException(GraphException::class);
        self::expectExceptionCode(1300);
        $graph->addNode($node2);
    }

    public function testGraphWhenNodeDoesNotExist(): void
    {
        $graph = new Graph('Test Graph');

        self::expectException(GraphException::class);
        self::expectExceptionCode(1301);
        $graph->addEdge(new Edge('text_1', 'text_2', 'Continue to text 2'));
    }

    public function testGraphWhenRemoveNodeDoesNotExist(): void
    {
        $graph = new Graph('Test Graph');

        self::expectException(GraphException::class);
        self::expectExceptionCode(1303);
        $graph->removeNode('text_1');
    }

    public function testGraphWhenRemoveEdgeDoesNotExist(): void
    {
        $graph = new Graph('Test Graph');

        self::expectException(GraphException::class);
        self::expectExceptionCode(1304);
        $graph->removeEdgeBetweenNodes('text_2', 'text_3');
    }
}
