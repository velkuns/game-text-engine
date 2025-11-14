<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Api;

use Velkuns\GameTextEngine\Core\Factory\TraitFactory;
use Velkuns\GameTextEngine\Exception\Api\AttributesApiException;
use Velkuns\GameTextEngine\Exception\Api\TraitsApiException;
use Velkuns\GameTextEngine\Rpg\Entity\EntityTraits;
use Velkuns\GameTextEngine\Rpg\Trait\TraitInterface;
use Velkuns\GameTextEngine\Rules\Traits\TraitsRules;
use Velkuns\GameTextEngine\Rules\Traits\TraitsRulesLeveling;
use Velkuns\GameTextEngine\Rules\Traits\TraitsRulesStarting;

/**
 * @phpstan-import-type TraitsData from EntityTraits
 * @phpstan-import-type TraitData from TraitInterface
 * @phpstan-import-type TraitsRulesData from TraitsRules
 */
class TraitsApi
{
    public TraitsRules $rules;

    public function __construct(
        private readonly TraitFactory $traitFactory,
    ) {}

    /**
     * @phpstan-param TraitsRulesData $data
     */
    public function load(array $data): void
    {
        $description = $data['description'];
        $starting    = new TraitsRulesStarting(...$data['starting']);
        $leveling    = new TraitsRulesLeveling(...$data['leveling']);

        $traits = [];
        foreach ($data['traits'] as $type => $list) {
            $traits[$type] = [];
            foreach ($list as $traitData) {
                $trait = $this->traitFactory->from($traitData);
                $traits[$type][$trait->getName()] = $trait;
            }
        }

        $this->rules = new TraitsRules($description, $starting, $leveling, $traits);
    }

    /**
     * @return array<string, array<string, TraitInterface>>
     */
    public function getAll(): array
    {
        return $this->rules->traits;
    }

    public function get(string $type, string $name, bool $asClone = true): ?TraitInterface
    {
        $trait = $this->rules->traits[$type][$name] ?? null;

        return $asClone ? $trait?->clone() : $trait;
    }

    public function set(TraitInterface $trait): self
    {
        if (!isset($this->rules->traits[$trait->getType()])) {
            throw new TraitsApiException("Unknown trait type '{$trait->getType()}'", 1550);
        }

        $this->rules->traits[$trait->getType()][$trait->getName()] = $trait;

        return $this;
    }

    public function remove(string $type, string $name): self
    {
        if (!isset($this->rules->traits[$type][$name])) {
            throw new TraitsApiException("The trait '$name' with type '$type' does not exist.", 1551);
        }

        unset($this->rules->traits[$type][$name]);

        return $this;
    }

    /**
     * @param array<string, list<string>> $data
     * @phpstan-return TraitsData
     */
    public function fromNewPlayer(array $data): array
    {
        $traits = [];

        //~ Pre-build $traits
        foreach ($this->rules->starting->attributions as $type => $value) {
            $traits[$type] = [];
        }

        //~ Transform attributes into data and separate bases with ou without init rule.
        foreach ($data as $type => $list) {
            if (!isset($traits[$type])) {
                throw new TraitsApiException("Unknown trait '$type'", 1552);
            }

            foreach ($list as $name) {
                $trait = $this->get($type, $name, false);
                if ($trait === null) {
                    throw new TraitsApiException("Unknown trait '$name' with type '$type'", 1553);
                }

                $traits[$type][$name] = $trait->jsonSerialize();
            }

            $remainingTrait = $this->rules->starting->attributions[$type] - \count($traits[$type]);
            if ($remainingTrait < 0) {
                throw new TraitsApiException("Too much trait for type '$type'", 1554);
            }

            if ($remainingTrait > 0) {
                throw new TraitsApiException("You must choose some other(s) trait for '$type'", 1555);
            }
        }

        return $traits;
    }

    public function dump(bool $prettyPrint = false): string
    {
        try {
            return \json_encode($this->rules, flags: \JSON_THROW_ON_ERROR | ($prettyPrint ? \JSON_PRETTY_PRINT : 0));
            // @codeCoverageIgnoreStart
        } catch (\JsonException $exception) {
            throw new AttributesApiException('Unable to dump traits rules data: ' . $exception->getMessage(), 1451, $exception);
        }
        // @codeCoverageIgnoreEnd
    }
}
