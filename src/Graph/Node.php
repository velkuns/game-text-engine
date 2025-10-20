<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Graph;

/**
 * @phpstan-type NodeData array{metadata: array{text: string, trigger?: TriggerData|null}}
 * @phpstan-type TriggerData array{combat?: array{enemies: list<string>}}
 */
readonly class Node implements \JsonSerializable
{
    /**
     * @phpstan-param TriggerData $trigger
     */
    public function __construct(
        public string $id,
        public string $content,
        public ?array $trigger = null,
    ) {}

    /**
     * @phpstan-return NodeData
     */
    public function jsonSerialize(): array
    {
        $data = [
            'metadata' => [
                'text'    => $this->content,
            ],
        ];

        if ($this->trigger !== null) {
            $data['metadata']['trigger'] = $this->trigger;
        }

        return $data;
    }
}
