<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Factory;

use Velkuns\GameTextEngine\Element\Ability\BaseAbility;
use Velkuns\GameTextEngine\Element\Ability\CompoundAbility;
use Velkuns\GameTextEngine\Element\Condition\Conditions;
use Velkuns\GameTextEngine\Element\Entity\Entity;
use Velkuns\GameTextEngine\Element\Entity\EntityInterface;
use Velkuns\GameTextEngine\Element\Exception\ElementJsonParseException;
use Velkuns\GameTextEngine\Element\Item\Item;
use Velkuns\GameTextEngine\Element\Item\ItemInterface;
use Velkuns\GameTextEngine\Element\Modifier\Modifier;
use Velkuns\GameTextEngine\Element\Status\Status;
use Velkuns\GameTextEngine\Element\Status\StatusInterface;

/**
 * @phpstan-import-type BaseAbilityData from BaseAbility
 * @phpstan-import-type CompoundAbilityData from CompoundAbility
 * @phpstan-import-type EntityData from EntityInterface
 * @phpstan-import-type StatusData from StatusInterface
 * @phpstan-import-type ConditionsData from Conditions
 * @phpstan-import-type ModifierData from Modifier
 * @phpstan-import-type ItemData from ItemInterface
 */
readonly class ElementFactory
{
    public function __construct(
        private EntityFactory $entityFactory,
        private AbilityFactory $abilityFactory,
        private StatusFactory $statusFactory,
        private ItemFactory $itemFactory,
        private ConditionsFactory $conditionsFactory,
        private ModifierFactory $modifierFactory,
    ) {}

    /**
     * @throws ElementJsonParseException
     */
    public function entityFromJson(string $json): Entity
    {
        try {
            /** @var EntityData $data */
            $data = \json_decode($json, true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new ElementJsonParseException('Cannot parse json data from entity data', 2010, $exception);
        }

        return $this->entityFactory->from($data);
    }

    /**
     * @throws ElementJsonParseException
     */
    public function abilityBaseFromJson(string $json): BaseAbility
    {
        try {
            /** @var BaseAbilityData $data */
            $data = \json_decode($json, true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new ElementJsonParseException('Cannot parse json data from base ability data', 2011, $exception);
        }

        return $this->abilityFactory->fromBaseAbility($data);
    }

    /**
     * @param array<string, BaseAbility> $bases
     * @throws ElementJsonParseException
     */
    public function abilityCompoundFromJson(string $json, array $bases): CompoundAbility
    {
        try {
            /** @var CompoundAbilityData $data */
            $data = \json_decode($json, true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new ElementJsonParseException('Cannot parse json data from compound ability data', 2012, $exception);
        }

        return $this->abilityFactory->fromCompoundAbility($data, $bases);
    }

    /**
     * @throws ElementJsonParseException
     */
    public function statusFromJson(string $json): Status
    {
        try {
            /** @var StatusData $data */
            $data = \json_decode($json, true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new ElementJsonParseException('Cannot parse json data from status data', 2013, $exception);
        }

        return $this->statusFactory->from($data);
    }

    /**
     * @throws ElementJsonParseException
     */
    public function modifierFromJson(string $json): Modifier
    {
        try {
            /** @var ModifierData $data */
            $data = \json_decode($json, true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new ElementJsonParseException('Cannot parse json data from modifier data', 2014, $exception);
        }

        return $this->modifierFactory->from($data);
    }

    /**
     * @throws ElementJsonParseException
     */
    public function conditionsFromJson(string $json): Conditions
    {
        try {
            /** @var ConditionsData $data */
            $data = \json_decode($json, true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new ElementJsonParseException('Cannot parse json data from condition data', 2015, $exception);
        }

        $conditions = $this->conditionsFactory->from($data);
        if ($conditions === null) {
            throw new ElementJsonParseException('Condition data cannot be null', 2016);
        }

        return $conditions;
    }

    /**
     * @throws ElementJsonParseException
     */
    public function itemFromJson(string $json): Item
    {
        try {
            /** @var ItemData $data */
            $data = \json_decode($json, true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new ElementJsonParseException('Cannot parse json data from item data', 2016, $exception);
        }

        return $this->itemFactory->from($data);
    }
}
