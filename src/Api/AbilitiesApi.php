<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Api;

use Velkuns\GameTextEngine\Core\Factory\AbilityFactory;
use Velkuns\GameTextEngine\Exception\Api\AbilitiesApiException;
use Velkuns\GameTextEngine\Rpg\Ability\AbilityInterface;
use Velkuns\GameTextEngine\Rpg\Ability\AbilityType;
use Velkuns\GameTextEngine\Rpg\Ability\BaseAbility;
use Velkuns\GameTextEngine\Rpg\Ability\CompoundAbility;
use Velkuns\GameTextEngine\Rpg\Entity\EntityAbilities;
use Velkuns\GameTextEngine\Rules\Abilities\AbilitiesRules;
use Velkuns\GameTextEngine\Rules\Abilities\AbilitiesRulesLeveling;
use Velkuns\GameTextEngine\Rules\Abilities\AbilitiesRulesStarting;

/**
 * @phpstan-import-type BaseAbilityData from BaseAbility
 * @phpstan-import-type CompoundAbilityData from CompoundAbility
 * @phpstan-import-type AbilitiesData from EntityAbilities
 * @phpstan-import-type AbilitiesRulesData from AbilitiesRules
 */
class AbilitiesApi
{
    public AbilitiesRules $rules;

    public function __construct(
        private readonly AbilityFactory $abilityFactory,
    ) {}

    /**
     * @phpstan-param AbilitiesRulesData $data
     */
    public function load(array $data): void
    {
        $description       = $data['description'];

        $starting          = new AbilitiesRulesStarting(...$data['starting']);
        $leveling          = new AbilitiesRulesLeveling(...$data['leveling']);

        $basesAbilities    = $this->abilityFactory->fromBases($data['bases']);
        $compoundAbilities = $this->abilityFactory->fromCompounds($data['compounds'], $basesAbilities);

        $this->rules = new AbilitiesRules(
            $description,
            $starting,
            $leveling,
            $basesAbilities,
            $compoundAbilities,
        );
    }

    /**
     * @return array{bases: array<string, BaseAbility>, compounds: array<string, CompoundAbility>}
     */
    public function getAll(): array
    {
        return (['bases' => $this->rules->baseAbilities, 'compounds' => $this->rules->compoundAbilities]);
    }

    public function get(string $name, bool $asClone = true): ?AbilityInterface
    {
        $ability = $this->rules->baseAbilities[$name] ?? $this->rules->compoundAbilities[$name] ?? null;

        if ($ability !== null && $asClone) {
            return $ability->getType() === AbilityType::Base ? $ability->clone() : $ability->clone($this->rules->baseAbilities);
        }

        return $ability;
    }

    public function set(AbilityInterface $ability): self
    {
        if ($ability instanceof BaseAbility) {
            $this->rules->baseAbilities[$ability->getName()] = $ability;
        } elseif ($ability instanceof CompoundAbility) {
            $this->rules->compoundAbilities[$ability->getName()] = $ability;
        }

        return $this;
    }

    public function remove(string $name): self
    {
        if (isset($this->rules->baseAbilities[$name])) {
            unset($this->rules->baseAbilities[$name]);
        } elseif (isset($this->rules->compoundAbilities[$name])) {
            unset($this->rules->compoundAbilities[$name]);
        } else {
            throw new AbilitiesApiException("The ability '$name' does not exist.", 1450);
        }

        return $this;
    }

    /**
     * @param array<string, int> $data
     * @phpstan-return AbilitiesData
     */
    public function fromNewPlayer(array $data): array
    {
        $abilities = ['bases' => [], 'compounds' => []];

        /** @var array<string, BaseAbilityData> $basesWithInitRule */
        $basesWithInitRule = [];
        /** @var array<string, BaseAbilityData> $basesWithInitRule */
        $bases = [];

        //~ Transform abilities into data and separate bases with ou without init rule.
        foreach ($this->rules->baseAbilities as $name => $ability) {
            if ($ability->getRule() !== null) {
                //~ Reset values before store ability with init rule
                $basesWithInitRule[$name] = ['initial' => 0, 'max' => 0, 'value' => 0] + $ability->jsonSerialize();
            } else {
                $bases[$name] = $ability->jsonSerialize();
            }
        }

        //~ Then, iterate on new player data abilities
        $totalAttributedPoints = 0;
        foreach ($data as $name => $value) {
            $ability = $bases[$name] ?? null;
            if ($ability === null) {
                throw new AbilitiesApiException("The ability '$name' does not exist.", 1451);
            }

            if ($value - $ability['value'] > $this->rules->starting->attributionPointsMaxPerAbility) {
                throw new AbilitiesApiException("You cannot attribute more than {$this->rules->starting->attributionPointsMaxPerAbility} per ability.", 1452);
            }

            if ($value < $ability['value']) {
                throw new AbilitiesApiException("You cannot have less point that base define for an ability.", 1453);
            }

            $totalAttributedPoints += $value - $ability['value'];

            //~ Set appropriate values
            $ability['initial'] = $value;
            $ability['max']     = $value;
            $ability['value']   = $value;

            //~ Then create
            $abilities['bases'][$name] = $ability;
        }

        $remainingPoints = $this->rules->starting->attributionPoints - $totalAttributedPoints;
        if ($remainingPoints < 0) {
            throw new AbilitiesApiException("You cannot attribute more than {$this->rules->starting->attributionPoints} in total.", 1454);
        }

        if ($remainingPoints > 0) {
            throw new AbilitiesApiException("You have still have some point(s) to attribute (remaining: $remainingPoints).", 1455);
        }

        //~ Then add base abilities with init rule
        foreach ($basesWithInitRule as $name => $ability) {
            $abilities['bases'][$name] = $ability;
        }

        //~ Then add compound abilities
        foreach ($this->rules->compoundAbilities as $name => $ability) {
            $abilities['compounds'][$name] = $ability->jsonSerialize();
        }

        return $abilities;
    }

    public function dump(bool $prettyPrint = false): string
    {
        try {
            return \json_encode($this->rules, flags: \JSON_THROW_ON_ERROR | ($prettyPrint ? \JSON_PRETTY_PRINT : 0));
            // @codeCoverageIgnoreStart
        } catch (\JsonException $exception) {
            throw new AbilitiesApiException('Unable to dump abilities rules data: ' . $exception->getMessage(), 1451, $exception);
        }
        // @codeCoverageIgnoreEnd
    }
}
