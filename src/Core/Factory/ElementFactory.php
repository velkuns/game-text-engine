<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Factory;

use Velkuns\GameTextEngine\Core\Prerequisite\Prerequisites;
use Velkuns\GameTextEngine\Exception\Core\ElementJsonParseException;
use Velkuns\GameTextEngine\Rpg\Attribute\BaseAttribute;
use Velkuns\GameTextEngine\Rpg\Attribute\CompoundAttribute;
use Velkuns\GameTextEngine\Rpg\Entity\EntityInterface;
use Velkuns\GameTextEngine\Rpg\Item\ItemInterface;
use Velkuns\GameTextEngine\Rpg\Modifier\Modifier;
use Velkuns\GameTextEngine\Rpg\Trait\EntityTrait;
use Velkuns\GameTextEngine\Rpg\Trait\TraitInterface;

/**
 * @phpstan-import-type BaseAttributeData from BaseAttribute
 * @phpstan-import-type CompoundAttributeData from CompoundAttribute
 * @phpstan-import-type EntityData from EntityInterface
 * @phpstan-import-type TraitData from TraitInterface
 * @phpstan-import-type PrerequisitesData from Prerequisites
 * @phpstan-import-type ModifierData from Modifier
 * @phpstan-import-type ItemData from ItemInterface
 */
readonly class ElementFactory
{
    public function __construct(
        private EntityFactory $entityFactory,
        private AttributeFactory $attributeFactory,
        private TraitFactory $traitFactory,
        private ItemFactory $itemFactory,
        private PrerequisitesFactory $prerequisitesFactory,
        private ModifierFactory $modifierFactory,
    ) {}

    /**
     * @throws ElementJsonParseException
     */
    public function entityFromJson(string $json): EntityInterface
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
    public function attributeBaseFromJson(string $json): BaseAttribute
    {
        try {
            /** @var BaseAttributeData $data */
            $data = \json_decode($json, true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new ElementJsonParseException('Cannot parse json data from base attribute data', 2011, $exception);
        }

        return $this->attributeFactory->fromBaseAttribute($data);
    }

    /**
     * @param array<string, BaseAttribute> $bases
     * @throws ElementJsonParseException
     */
    public function attributeCompoundFromJson(string $json, array $bases): CompoundAttribute
    {
        try {
            /** @var CompoundAttributeData $data */
            $data = \json_decode($json, true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new ElementJsonParseException('Cannot parse json data from compound attribute data', 2012, $exception);
        }

        return $this->attributeFactory->fromCompoundAttribute($data, $bases);
    }

    /**
     * @throws ElementJsonParseException
     */
    public function traitFromJson(string $json): EntityTrait
    {
        try {
            /** @var TraitData $data */
            $data = \json_decode($json, true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new ElementJsonParseException('Cannot parse json data from trait data', 2013, $exception);
        }

        return $this->traitFactory->from($data);
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
    public function prerequisitesFromJson(string $json): Prerequisites
    {
        try {
            /** @var PrerequisitesData $data */
            $data = \json_decode($json, true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new ElementJsonParseException('Cannot parse json data from prerequisites data', 2015, $exception);
        }

        $prerequisites = $this->prerequisitesFactory->from($data);
        if ($prerequisites === null) {
            throw new ElementJsonParseException('Prerequisites data cannot be null', 2016);
        }

        return $prerequisites;
    }

    /**
     * @throws ElementJsonParseException
     */
    public function itemFromJson(string $json): ItemInterface
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
