<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rules\Traits;

use Velkuns\GameTextEngine\Exception\Rules\TraitsRulesException;
use Velkuns\GameTextEngine\Rpg\Entity\EntityTraits;
use Velkuns\GameTextEngine\Rpg\Traits\TraitInterface;

/**
 * @phpstan-import-type TraitsData from EntityTraits
 * @phpstan-import-type TraitData from TraitInterface
 * @phpstan-import-type TraitsRulesStartingData from TraitsRulesStarting
 * @phpstan-import-type TraitsRulesLevelingData from TraitsRulesLeveling
 * @phpstan-type TraitsRulesData array{
 *     description: string,
 *     starting: TraitsRulesStartingData,
 *     leveling: TraitsRulesLevelingData,
 *     traits: array<string, list<TraitData>>,
 * }
 */
class TraitsRules implements \JsonSerializable
{
    /**
     * @param array<string, array<string, TraitInterface>> $traits
     */
    public function __construct(
        public string $description,
        public TraitsRulesStarting $starting,
        public TraitsRulesLeveling $leveling,
        public array $traits = [],
    ) {}

    /**
     * @param array<string, array<string>> $traits
     */
    public function assertAllTraitsExist(array $traits): void
    {
        foreach ($traits as $type => $list) {
            foreach ($list as $name) {
                $this->assertTraitExists($type, $name);
            }
        }
    }

    public function assertTraitExists(string $type, string $name): void
    {
        if (!isset($this->traits[$type][$name])) {
            throw new TraitsRulesException("Trait '$name' of type '$type' not found.", 2000);
        }
    }

    /**
     * @phpstan-return TraitsRulesData
     */
    public function jsonSerialize(): array
    {
        $traits = \array_map(
            fn(array $list) => \array_values(\array_map(fn(TraitInterface $trait) => $trait->jsonSerialize(), $list)),
            $this->traits,
        );

        return [
            'description' => $this->description,
            'starting'    => $this->starting->jsonSerialize(),
            'leveling'    => $this->leveling->jsonSerialize(),
            'traits'    => $traits,
        ];
    }
}
