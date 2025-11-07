<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Velkuns\GameTextEngine\Utils\Exporter;

use Velkuns\GameTextEngine\Exception\Utils\ExporterException;
use Velkuns\GameTextEngine\Graph\Graph;

readonly class DOTExporter
{
    public function toString(Graph $graph): string
    {
        $content = "digraph story {\n\n";

        $content .= "  node [shape=box,style=rounded]\n\n";

        foreach ($graph->getAllNodes() as $node) {
            $label    = $this->cleanText($node->content, 200);
            $content .= "  $node->id [label=\"$label\"]\n";
        }

        $content .= "\n";

        foreach ($graph->getAllEdges() as $edge) {
            $label    = $this->cleanText($edge->content, 30);
            $content .= "  $edge->source -> $edge->target [label=\"$label\"]\n";
        }

        $content .= "}\n";

        return $content;
    }

    /**
     * @codeCoverageIgnore
     */
    public function toFile(Graph $graph, string $filePathname): self
    {
        $result = \file_put_contents($filePathname, $this->toString($graph));

        if ($result === false) {
            throw new ExporterException("Unable to write file $filePathname! Is directory exists and writable ?");
        }

        return $this;
    }

    private function cleanText(string $text, int $length): string
    {
        if (strlen($text) > $length) {
            $text = substr($text, 0, $length) . '...'; // @codeCoverageIgnore
        }
        return \str_replace(['"', "\n"], ['\"', '\n'], $text);
    }
}
