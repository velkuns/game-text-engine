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

  text_1 [label="Start"]
  text_2 [label="2 -> 3, 2 -> 5"]
  text_3 [label="3 -> 4"]
  text_4 [label="4 -> Death, 4 -> End"]
  text_5 [label="5 -> End"]
  text_8 [label="Death"]
  text_9 [label="End"]

  text_1 -> text_2 [label="Continue to 2"]
  text_2 -> text_3 [label="Continue to 3"]
  text_3 -> text_4 [label="Continue to 4"]
  text_4 -> text_8 [label="Continue to death"]
  text_4 -> text_9 [label="Continue to end"]
  text_2 -> text_5 [label="Continue to 5"]
  text_5 -> text_9 [label="Continue to end"]
}
';

        $graph    = self::getComplexGraph();
        $exporter = new DOTExporter();

        $export = $exporter->toString($graph);

        self::assertSame($expected, $export);
    }
}
