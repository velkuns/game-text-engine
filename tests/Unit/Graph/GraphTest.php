<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Graph;

use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Exception\Graph\GraphException;
use Velkuns\GameTextEngine\Graph\Edge;
use Velkuns\GameTextEngine\Graph\Graph;
use Velkuns\GameTextEngine\Graph\Node;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;
use Velkuns\GameTextEngine\Tests\Helper\GraphTrait;

/**
 * @phpstan-import-type GraphData from Graph
 */
class GraphTest extends TestCase
{
    use FactoryTrait;
    use GraphTrait;

    public function testGraph(): void
    {
        $graph = new Graph('Test Graph');

        $trigger = ['combat' => ['enemies' => ['Goblin']]];

        $node1 = new Node('1', 'Text for node 1.');
        $node2 = new Node('2', 'Text for node 2.', false, $trigger);
        $edge  = new Edge('1', '2', 'Continue to text 2');
        $graph->addNode($node1);
        $graph->addNode($node2);
        $graph->addEdge($edge);


        self::assertSame($node1, $graph->getNode('1'));
        self::assertSame($node2, $graph->getNode('2'));
        self::assertEquals(['2' => $edge], $graph->getEdgesFromSource('1'));

        $data = [
            'metadata' => [
                'title' => 'Test Graph',
            ],
            'nodes' => [
                '1' => [
                    'metadata' => [
                        'text' => 'Text for node 1.',
                    ],
                ],
                '2' => [
                    'metadata' => [
                        'text' => 'Text for node 2.',
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
                    'source' => '1',
                    'target' => '2',
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
        $graph = self::getComplexGraph();

        $expectedBefore = [
            'metadata' => ['title' => 'Test Graph',],
            'nodes' => [
                '1' => ['metadata' => ['text' => 'Start']],
                '2' => ['metadata' => ['text' => '2 -> 3, 2 -> 5']],
                '3' => ['metadata' => ['text' => '3 -> 4']],
                '4' => ['metadata' => ['text' => '4 -> Death, 4 -> End']],
                '5' => ['metadata' => ['text' => '5 -> End']],
                '8' => ['metadata' => ['text' => 'Death']],
                '9' => ['metadata' => ['text' => 'End']],
            ],
            'edges' => [
                ['source' => '1', 'target' => '2', 'label'  => 'action', 'metadata' => ['text' => 'Continue to 2']],
                ['source' => '2', 'target' => '3', 'label'  => 'action', 'metadata' => ['text' => 'Continue to 3']],
                ['source' => '3', 'target' => '4', 'label'  => 'action', 'metadata' => ['text' => 'Continue to 4']],
                ['source' => '4', 'target' => '8', 'label'  => 'action', 'metadata' => ['text' => 'Continue to death']],
                ['source' => '4', 'target' => '9', 'label'  => 'action', 'metadata' => ['text' => 'Continue to end']],
                ['source' => '2', 'target' => '5', 'label'  => 'action', 'metadata' => ['text' => 'Continue to 5']],
                ['source' => '5', 'target' => '9', 'label'  => 'action', 'metadata' => ['text' => 'Continue to end']],
            ],
        ];

        $expectedAfterRemoveNode = [
            'metadata' => ['title' => 'Test Graph',],
            'nodes' => [
                '1' => ['metadata' => ['text' => 'Start']],
                '2' => ['metadata' => ['text' => '2 -> 3, 2 -> 5']],
                '3' => ['metadata' => ['text' => '3 -> 4']],
                '5' => ['metadata' => ['text' => '5 -> End']],
                '8' => ['metadata' => ['text' => 'Death']],
                '9' => ['metadata' => ['text' => 'End']],
            ],
            'edges' => [
                ['source' => '1', 'target' => '2', 'label'  => 'action', 'metadata' => ['text' => 'Continue to 2']],
                ['source' => '2', 'target' => '3', 'label'  => 'action', 'metadata' => ['text' => 'Continue to 3']],
                ['source' => '2', 'target' => '5', 'label'  => 'action', 'metadata' => ['text' => 'Continue to 5']],
                ['source' => '5', 'target' => '9', 'label'  => 'action', 'metadata' => ['text' => 'Continue to end']],
            ],
        ];

        //~ Remove node 4. That will remove edges FROM and TO this node
        $expectedAfterRemoveEdge = [
            'metadata' => ['title' => 'Test Graph',],
            'nodes' => [
                '1' => ['metadata' => ['text' => 'Start']],
                '2' => ['metadata' => ['text' => '2 -> 3, 2 -> 5']],
                '3' => ['metadata' => ['text' => '3 -> 4']],
                '5' => ['metadata' => ['text' => '5 -> End']],
                '8' => ['metadata' => ['text' => 'Death']],
                '9' => ['metadata' => ['text' => 'End']],
            ],
            'edges' => [
                ['source' => '1', 'target' => '2', 'label'  => 'action', 'metadata' => ['text' => 'Continue to 2']],
                ['source' => '2', 'target' => '5', 'label'  => 'action', 'metadata' => ['text' => 'Continue to 5']],
                ['source' => '5', 'target' => '9', 'label'  => 'action', 'metadata' => ['text' => 'Continue to end']],
            ],
        ];

        //~ Assert graph is ok
        self::assertSame($expectedBefore, $graph->jsonSerialize());


        //~ Remove node 4. That will remove edges FROM and TO this node
        $graph->removeNode('4');
        self::assertSame($expectedAfterRemoveNode, $graph->jsonSerialize());

        //~ Assert graph is ok after the remove of single edge between two node
        $graph->removeEdgeBetweenNodes('2', '3');
        self::assertSame($expectedAfterRemoveEdge, $graph->jsonSerialize());
    }

    public function testGraphWhenTryToAddEdgeButNodeDoesNotExist(): void
    {
        $graph = new Graph('Test Graph');

        self::expectException(GraphException::class);
        self::expectExceptionCode(1302);
        $graph->getNode('1');
    }

    public function testGraphWhenAddNodeButNodeWithSameIdAlreadyExists(): void
    {
        $graph = new Graph('Test Graph');
        $node1 = new Node('1', 'Text for node 1.');
        $node2 = new Node('1', 'Text for node 2.');
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
        $graph->addEdge(new Edge('1', '2', 'Continue to text 2'));
    }

    public function testGraphWhenRemoveNodeDoesNotExist(): void
    {
        $graph = new Graph('Test Graph');

        self::expectException(GraphException::class);
        self::expectExceptionCode(1303);
        $graph->removeNode('1');
    }

    public function testGraphWhenRemoveEdgeDoesNotExist(): void
    {
        $graph = new Graph('Test Graph');

        self::expectException(GraphException::class);
        self::expectExceptionCode(1304);
        $graph->removeEdgeBetweenNodes('2', '3');
    }
}
