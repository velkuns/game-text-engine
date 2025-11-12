<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Api;

use Velkuns\GameTextEngine\Core\Factory\AttributeFactory;
use Velkuns\GameTextEngine\Exception\Api\AttributesApiException;
use Velkuns\GameTextEngine\Rpg\Attribute\AttributeInterface;
use Velkuns\GameTextEngine\Rpg\Attribute\AttributeType;
use Velkuns\GameTextEngine\Rpg\Attribute\BaseAttribute;
use Velkuns\GameTextEngine\Rpg\Attribute\CompoundAttribute;
use Velkuns\GameTextEngine\Rpg\Entity\EntityAttributes;
use Velkuns\GameTextEngine\Rules\Attributes\AttributesRules;
use Velkuns\GameTextEngine\Rules\Attributes\AttributesRulesLeveling;
use Velkuns\GameTextEngine\Rules\Attributes\AttributesRulesStarting;

/**
 * @phpstan-import-type BaseAttributeData from BaseAttribute
 * @phpstan-import-type CompoundAttributeData from CompoundAttribute
 * @phpstan-import-type AttributesData from EntityAttributes
 * @phpstan-import-type AttributesRulesData from AttributesRules
 */
class AttributesApi
{
    public AttributesRules $rules;

    public function __construct(
        private readonly AttributeFactory $attributeFactory,
    ) {}

    /**
     * @phpstan-param AttributesRulesData $data
     */
    public function load(array $data): void
    {
        $description       = $data['description'];

        $starting          = new AttributesRulesStarting(...$data['starting']);
        $leveling          = new AttributesRulesLeveling(...$data['leveling']);

        $basesAttributes    = $this->attributeFactory->fromBases($data['bases']);
        $compoundAttributes = $this->attributeFactory->fromCompounds($data['compounds'], $basesAttributes);

        $this->rules = new AttributesRules(
            $description,
            $starting,
            $leveling,
            $basesAttributes,
            $compoundAttributes,
        );
    }

    /**
     * @return array{bases: array<string, BaseAttribute>, compounds: array<string, CompoundAttribute>}
     */
    public function getAll(): array
    {
        return (['bases' => $this->rules->baseAttributes, 'compounds' => $this->rules->compoundAttributes]);
    }

    public function get(string $name, bool $asClone = true): ?AttributeInterface
    {
        $attribute = $this->rules->baseAttributes[$name] ?? $this->rules->compoundAttributes[$name] ?? null;

        if ($attribute !== null && $asClone) {
            return $attribute->getType() === AttributeType::Base ? $attribute->clone() : $attribute->clone($this->rules->baseAttributes);
        }

        return $attribute;
    }

    public function set(AttributeInterface $attribute): self
    {
        if ($attribute instanceof BaseAttribute) {
            $this->rules->baseAttributes[$attribute->getName()] = $attribute;
        } elseif ($attribute instanceof CompoundAttribute) {
            $this->rules->compoundAttributes[$attribute->getName()] = $attribute;
        }

        return $this;
    }

    public function remove(string $name): self
    {
        if (isset($this->rules->baseAttributes[$name])) {
            unset($this->rules->baseAttributes[$name]);
        } elseif (isset($this->rules->compoundAttributes[$name])) {
            unset($this->rules->compoundAttributes[$name]);
        } else {
            throw new AttributesApiException("The attribute '$name' does not exist.", 1450);
        }

        return $this;
    }

    /**
     * @param array<string, int> $data
     * @phpstan-return AttributesData
     */
    public function fromNewPlayer(array $data): array
    {
        $attributes = ['bases' => [], 'compounds' => []];

        /** @var array<string, BaseAttributeData> $basesWithInitRule */
        $basesWithInitRule = [];
        /** @var array<string, BaseAttributeData> $basesWithInitRule */
        $bases = [];

        //~ Transform attributes into data and separate bases with ou without init rule.
        foreach ($this->rules->baseAttributes as $name => $attribute) {
            if ($attribute->getRule() !== null) {
                //~ Reset values before store attribute with init rule
                $basesWithInitRule[$name] = ['initial' => 0, 'max' => 0, 'value' => 0] + $attribute->jsonSerialize();
            } else {
                $bases[$name] = $attribute->jsonSerialize();
            }
        }

        //~ Then, iterate on new player data attributes
        $totalAttributedPoints = 0;
        foreach ($data as $name => $value) {
            $attribute = $bases[$name] ?? null;
            if ($attribute === null) {
                throw new AttributesApiException("The attribute '$name' does not exist.", 1451);
            }

            if ($value - $attribute['value'] > $this->rules->starting->attributionPointsMaxPerAttribute) {
                throw new AttributesApiException("You cannot attribute more than {$this->rules->starting->attributionPointsMaxPerAttribute} per attribute.", 1452);
            }

            if ($value < $attribute['value']) {
                throw new AttributesApiException("You cannot have less point that base define for an attribute.", 1453);
            }

            $totalAttributedPoints += $value - $attribute['value'];

            //~ Set appropriate values
            $attribute['initial'] = $value;
            $attribute['max']     = $value;
            $attribute['value']   = $value;

            //~ Then create
            $attributes['bases'][$name] = $attribute;
        }

        $remainingPoints = $this->rules->starting->attributionPoints - $totalAttributedPoints;
        if ($remainingPoints < 0) {
            throw new AttributesApiException("You cannot attribute more than {$this->rules->starting->attributionPoints} in total.", 1454);
        }

        if ($remainingPoints > 0) {
            throw new AttributesApiException("You have still have some point(s) to attribute (remaining: $remainingPoints).", 1455);
        }

        //~ Then add base attributes with init rule
        foreach ($basesWithInitRule as $name => $attribute) {
            $attributes['bases'][$name] = $attribute;
        }

        //~ Then add compound attributes
        foreach ($this->rules->compoundAttributes as $name => $attribute) {
            $attributes['compounds'][$name] = $attribute->jsonSerialize();
        }

        return $attributes;
    }

    public function dump(bool $prettyPrint = false): string
    {
        try {
            return \json_encode($this->rules, flags: \JSON_THROW_ON_ERROR | ($prettyPrint ? \JSON_PRETTY_PRINT : 0));
            // @codeCoverageIgnoreStart
        } catch (\JsonException $exception) {
            throw new AttributesApiException('Unable to dump attributes rules data: ' . $exception->getMessage(), 1451, $exception);
        }
        // @codeCoverageIgnoreEnd
    }
}
