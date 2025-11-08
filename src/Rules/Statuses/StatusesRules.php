<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rules\Statuses;

use Velkuns\GameTextEngine\Exception\Rules\StatusesRulesException;
use Velkuns\GameTextEngine\Rpg\Entity\EntityStatuses;
use Velkuns\GameTextEngine\Rpg\Status\StatusInterface;

/**
 * @phpstan-import-type StatusesData from EntityStatuses
 * @phpstan-import-type StatusData from StatusInterface
 * @phpstan-import-type StatusesRulesStartingData from StatusesRulesStarting
 * @phpstan-import-type StatusesRulesLevelingData from StatusesRulesLeveling
 * @phpstan-type StatusesRulesData array{
 *     description: string,
 *     starting: StatusesRulesStartingData,
 *     leveling: StatusesRulesLevelingData,
 *     statuses: array<string, list<StatusData>>,
 * }
 */
class StatusesRules implements \JsonSerializable
{
    /**
     * @param array<string, array<string, StatusInterface>> $statuses
     */
    public function __construct(
        public string $description,
        public StatusesRulesStarting $starting,
        public StatusesRulesLeveling $leveling,
        public array $statuses = [],
    ) {}

    /**
     * @param array<string, array<string>> $statuses
     */
    public function assertAllStatusesExist(array $statuses): void
    {
        foreach ($statuses as $type => $list) {
            foreach ($list as $name) {
                $this->assertStatusExists($type, $name);
            }
        }
    }

    public function assertStatusExists(string $type, string $name): void
    {
        if (!isset($this->statuses[$type][$name])) {
            throw new StatusesRulesException("Status '$name' of type '$type' not found.", 2000);
        }
    }

    /**
     * @phpstan-return StatusesRulesData
     */
    public function jsonSerialize(): array
    {
        $statuses = \array_map(
            fn(array $list) => \array_values(\array_map(fn(StatusInterface $status) => $status->jsonSerialize(), $list)),
            $this->statuses,
        );

        return [
            'description' => $this->description,
            'starting'    => $this->starting->jsonSerialize(),
            'leveling'    => $this->leveling->jsonSerialize(),
            'statuses'    => $statuses,
        ];
    }
}
