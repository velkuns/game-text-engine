<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Unit\Graph;

use Velkuns\GameTextEngine\Graph\Edge;
use Velkuns\GameTextEngine\Graph\Exception\GraphException;
use Velkuns\GameTextEngine\Graph\Graph;
use Velkuns\GameTextEngine\Graph\Node;
use PHPUnit\Framework\TestCase;

class GraphTest extends TestCase
{
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
        self::assertSame([$edge], $graph->getEdges('text_1'));

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
}
