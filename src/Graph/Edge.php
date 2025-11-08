<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Graph;

use Velkuns\GameTextEngine\Core\Condition\Conditions;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;

/**
 * @phpstan-import-type ConditionsData from Conditions
 * @phpstan-type EdgeData array{source: string, target: string, label: string, metadata: array{text: string, conditions?: ConditionsData|null}}
 */
readonly class Edge implements \JsonSerializable
{
    public function __construct(
        public string $source,
        public string $target,
        public string $content,
        public ?Conditions $conditions = null,
    ) {}

    public function available(EntityInterface $player, ?EntityInterface $enemy = null): bool
    {
        return $this->conditions?->evaluate($player, $enemy) ?? true;
    }

    /**
     * @phpstan-return EdgeData
     */
    public function jsonSerialize(): array
    {
        $data = [
            'source'   => $this->source,
            'target'   => $this->target,
            'label'    => 'action',
            'metadata' => [
                'text' => $this->content,
            ],
        ];

        if ($this->conditions !== null) {
            $data['metadata']['conditions'] = $this->conditions->jsonSerialize();
        }

        return $data;
    }
}
