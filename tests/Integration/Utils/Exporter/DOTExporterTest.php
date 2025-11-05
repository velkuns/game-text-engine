<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Integration\Utils\Exporter;

use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Tests\Helper\GraphTrait;
use Velkuns\GameTextEngine\Utils\Exporter\DOTExporter;

class DOTExporterTest extends TestCase
{
    use GraphTrait;

    public function testExportString(): void
    {
        $expected = 'digraph story {

  node [shape=box,style=rounded]

  1 [label="Start"]
  2 [label="2 -> 3, 2 -> 5"]
  3 [label="3 -> 4"]
  4 [label="4 -> Death, 4 -> End"]
  5 [label="5 -> End"]
  8 [label="Death"]
  9 [label="End"]

  1 -> 2 [label="Continue to 2"]
  2 -> 3 [label="Continue to 3"]
  3 -> 4 [label="Continue to 4"]
  4 -> 8 [label="Continue to death"]
  4 -> 9 [label="Continue to end"]
  2 -> 5 [label="Continue to 5"]
  5 -> 9 [label="Continue to end"]
}
';

        $graph    = self::getComplexGraph();
        $exporter = new DOTExporter();

        $export = $exporter->toString($graph);

        self::assertSame($expected, $export);
    }
}
