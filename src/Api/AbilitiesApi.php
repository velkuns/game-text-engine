<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Api;

use Velkuns\GameTextEngine\Api\Exception\AbilitiesApiException;
use Velkuns\GameTextEngine\Element\Ability\AbilityInterface;
use Velkuns\GameTextEngine\Element\Ability\BaseAbility;
use Velkuns\GameTextEngine\Element\Ability\CompoundAbility;
use Velkuns\GameTextEngine\Element\Entity\EntityAbilities;
use Velkuns\GameTextEngine\Element\Factory\AbilityFactory;

/**
 * @phpstan-import-type BaseAbilityData from BaseAbility
 * @phpstan-import-type CompoundAbilityData from CompoundAbility
 * @phpstan-import-type AbilitiesData from EntityAbilities
 * @phpstan-type AbilitiesRulesData array{
 *    description: string,
 *    attributionPoints: int,
 *    attributionPointsMaxPerAbility: 5,
 *    bases: array<string, BaseAbilityData>,
 *    compounds: array<string, CompoundAbilityData>,
 * }
 */
class AbilitiesApi
{
    public string $description = '';
    public int $attributionPoints = 0;
    public int $attributionPointsMaxPerAbility = 0;

    /** @var array<string, BaseAbility> $baseAbilities */
    public array $baseAbilities = [];

    /** @var array<string, CompoundAbility> */
    public array $compoundAbilities = [];

    public function __construct(
        private readonly AbilityFactory $abilityFactory,
    ) {}

    /**
     * @phpstan-param AbilitiesRulesData $data
     */
    public function load(array $data): void
    {
        $this->description                    = $data['description'];
        $this->attributionPoints              = $data['attributionPoints'];
        $this->attributionPointsMaxPerAbility = $data['attributionPointsMaxPerAbility'];

        $this->baseAbilities     = $this->abilityFactory->fromBases($data['bases']);
        $this->compoundAbilities = $this->abilityFactory->fromCompounds($data['compounds'], $this->baseAbilities);
    }

    /**
     * @return array{bases: array<string, BaseAbility>, compounds: array<string, CompoundAbility>}
     */
    public function getAll(): array
    {
        return (['bases' => $this->baseAbilities, 'compounds' => $this->compoundAbilities]);
    }

    public function get(string $name, bool $asClone = true): ?AbilityInterface
    {
        $ability = $this->baseAbilities[$name] ?? $this->compoundAbilities[$name] ?? null;

        if ($ability !== null && $asClone) {
            return $ability->clone();
        }

        return $ability;
    }

    public function set(AbilityInterface $ability): self
    {
        if ($ability instanceof BaseAbility) {
            $this->baseAbilities[$ability->getName()] = $ability;
        } elseif ($ability instanceof CompoundAbility) {
            $this->compoundAbilities[$ability->getName()] = $ability;
        }

        return $this;
    }

    public function remove(string $name): self
    {
        if (isset($this->baseAbilities[$name])) {
            unset($this->baseAbilities[$name]);
        } elseif (isset($this->compoundAbilities[$name])) {
            unset($this->compoundAbilities[$name]);
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
        foreach ($this->baseAbilities as $name => $ability) {
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

            if ($value - $ability['value'] > $this->attributionPointsMaxPerAbility) {
                throw new AbilitiesApiException("You cannot attribute more than $this->attributionPointsMaxPerAbility per ability.", 1452);
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

        $remainingPoints = $this->attributionPoints - $totalAttributedPoints;
        if ($remainingPoints < 0) {
            throw new AbilitiesApiException("You cannot attribute more than $this->attributionPoints in total.", 1454);
        }

        if ($remainingPoints > 0) {
            throw new AbilitiesApiException("You have still have some point(s) to attribute (remaining: $remainingPoints).", 1455);
        }

        //~ Then add base abilities with init rule
        foreach ($basesWithInitRule as $name => $ability) {
            $abilities['bases'][$name] = $ability;
        }

        //~ Then add compound abilities
        foreach ($this->compoundAbilities as $name => $ability) {
            $abilities['compounds'][$name] = $ability->jsonSerialize();
        }

        return $abilities;
    }

    public function dump(bool $prettyPrint = false): string
    {
        //~ Before dump, we need to reset initial/max/value for ability with init rule.
        $baseAbilities = $this->baseAbilities;
        foreach ($baseAbilities as $ability) {
            if ($ability->getRule() === null) {
                continue;
            }

            $ability->value   = 0;
            $ability->initial = 0;
            $ability->max     = 0;
        }

        try {
            $data = [
                'description'                    => $this->description,
                'attributionPoints'              => $this->attributionPoints,
                'attributionPointsMaxPerAbility' => $this->attributionPointsMaxPerAbility,
                'bases'                          => $baseAbilities,
                'compounds'                      => $this->compoundAbilities,
            ];

            return \json_encode($data, flags: \JSON_THROW_ON_ERROR | ($prettyPrint ? \JSON_PRETTY_PRINT : 0));
            // @codeCoverageIgnoreStart
        } catch (\JsonException $exception) {
            throw new AbilitiesApiException('Unable to dump abilities rules data: ' . $exception->getMessage(), 1451, $exception);
        }
        // @codeCoverageIgnoreEnd
    }
}
