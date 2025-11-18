<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rules\Alterations;

use Velkuns\GameTextEngine\Exception\Rules\AlterationsRulesException;
use Velkuns\GameTextEngine\Rpg\Alteration\AlterationInterface;
use Velkuns\GameTextEngine\Rpg\Entity\EntityAlterations;

/**
 * @phpstan-import-type EntityAlterationsData from EntityAlterations
 * @phpstan-import-type AlterationData from AlterationInterface
 * @phpstan-import-type AlterationsRulesStartingData from AlterationsRulesStarting
 * @phpstan-import-type AlterationsRulesLevelingData from AlterationsRulesLeveling
 * @phpstan-type AlterationsRulesData array{
 *     description: string,
 *     starting: AlterationsRulesStartingData,
 *     leveling: AlterationsRulesLevelingData,
 *     alterations: array<string, list<AlterationData>>,
 * }
 */
class AlterationsRules implements \JsonSerializable
{
    /**
     * @param array<string, array<string, AlterationInterface>> $alterations
     */
    public function __construct(
        public string $description,
        public AlterationsRulesStarting $starting,
        public AlterationsRulesLeveling $leveling,
        public array $alterations = [],
    ) {}

    /**
     * @param array<string, array<string>> $alterations
     */
    public function assertAllAlterationsExist(array $alterations): void
    {
        foreach ($alterations as $type => $list) {
            foreach ($list as $name) {
                $this->assertAlterationExists($type, $name);
            }
        }
    }

    public function assertAlterationExists(string $type, string $name): void
    {
        if (!isset($this->alterations[$type][$name])) {
            throw new AlterationsRulesException("Alteration '$name' of type '$type' not found.", 2500);
        }
    }

    /**
     * @phpstan-return AlterationsRulesData
     */
    public function jsonSerialize(): array
    {
        $alterations = \array_map(
            fn(array $list) => \array_values(\array_map(fn(AlterationInterface $alteration) => $alteration->jsonSerialize(), $list)),
            $this->alterations,
        );

        return [
            'description' => $this->description,
            'starting'    => $this->starting->jsonSerialize(),
            'leveling'    => $this->leveling->jsonSerialize(),
            'alterations' => $alterations,
        ];
    }
}
