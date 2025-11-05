<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Helper;

use Velkuns\GameTextEngine\Graph\Edge;
use Velkuns\GameTextEngine\Graph\Graph;
use Velkuns\GameTextEngine\Graph\Node;

trait GraphTrait
{
    private static function getComplexGraph(): Graph
    {
        $graph = new Graph('Test Graph');

        $node1 = new Node('1', 'Start');
        $node2 = new Node('2', '2 -> 3, 2 -> 5');
        $node3 = new Node('3', '3 -> 4');
        $node4 = new Node('4', '4 -> Death, 4 -> End');
        $node5 = new Node('5', '5 -> End');
        $node8 = new Node('8', 'Death');
        $node9 = new Node('9', 'End');

        $edge1_2 = new Edge('1', '2', 'Continue to 2');
        $edge2_3 = new Edge('2', '3', 'Continue to 3');
        $edge3_4 = new Edge('3', '4', 'Continue to 4');
        $edge4_8 = new Edge('4', '8', 'Continue to death');
        $edge4_9 = new Edge('4', '9', 'Continue to end');
        $edge2_5 = new Edge('2', '5', 'Continue to 5');
        $edge5_9 = new Edge('5', '9', 'Continue to end');

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

        return $graph;
    }
}
