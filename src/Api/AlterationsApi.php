<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Api;

use Velkuns\GameTextEngine\Core\Factory\AlterationFactory;
use Velkuns\GameTextEngine\Exception\Api\AttributesApiException;
use Velkuns\GameTextEngine\Exception\Api\AlterationsApiException;
use Velkuns\GameTextEngine\Rpg\Entity\EntityAlterations;
use Velkuns\GameTextEngine\Rpg\Alteration\AlterationInterface;
use Velkuns\GameTextEngine\Rules\Alterations\AlterationsRules;
use Velkuns\GameTextEngine\Rules\Alterations\AlterationsRulesLeveling;
use Velkuns\GameTextEngine\Rules\Alterations\AlterationsRulesStarting;

/**
 * @phpstan-import-type EntityAlterationsData from EntityAlterations
 * @phpstan-import-type AlterationData from AlterationInterface
 * @phpstan-import-type AlterationsRulesData from AlterationsRules
 */
class AlterationsApi
{
    public AlterationsRules $rules;

    public function __construct(
        private readonly AlterationFactory $alterationFactory,
    ) {}

    /**
     * @phpstan-param AlterationsRulesData $data
     */
    public function load(array $data): void
    {
        $description = $data['description'];
        $starting    = new AlterationsRulesStarting($data['starting']);
        $leveling    = new AlterationsRulesLeveling($data['leveling']);

        $alterations = [];
        foreach ($data['alterations'] as $type => $list) {
            $alterations[$type] = [];
            foreach ($list as $alterationData) {
                $alteration = $this->alterationFactory->from($alterationData);
                $alterations[$type][$alteration->getName()] = $alteration;
            }
        }

        $this->rules = new AlterationsRules($description, $starting, $leveling, $alterations);
    }

    /**
     * @return array<string, array<string, AlterationInterface>>
     */
    public function getAll(): array
    {
        return $this->rules->alterations;
    }

    public function get(string $type, string $name, bool $asClone = true): ?AlterationInterface
    {
        $alteration = $this->rules->alterations[$type][$name] ?? null;

        return $asClone ? $alteration?->clone() : $alteration;
    }

    public function set(AlterationInterface $alteration): self
    {
        if (!isset($this->rules->alterations[$alteration->getType()])) {
            throw new AlterationsApiException("Unknown alteration type '{$alteration->getType()}'", 1550);
        }

        $this->rules->alterations[$alteration->getType()][$alteration->getName()] = $alteration;

        return $this;
    }

    public function remove(string $type, string $name): self
    {
        if (!isset($this->rules->alterations[$type][$name])) {
            throw new AlterationsApiException("The alteration '$name' with type '$type' does not exist.", 1551);
        }

        unset($this->rules->alterations[$type][$name]);

        return $this;
    }

    /**
     * @param array<string, list<string>> $data
     * @phpstan-return EntityAlterationsData
     */
    public function fromNewPlayer(array $data): array
    {
        $alterations = [];

        //~ Pre-build $alterations
        foreach ($this->rules->starting->getAllTypes() as $type) {
            $alterations[$type] = [];
        }

        //~ Transform attributes into data and separate simples with ou without init rule.
        foreach ($data as $type => $list) {
            if (!isset($alterations[$type])) {
                throw new AlterationsApiException("Unknown alteration '$type'", 1552);
            }

            foreach ($list as $name) {
                $alteration = $this->get($type, $name, false);
                if ($alteration === null) {
                    throw new AlterationsApiException("Unknown alteration '$name' with type '$type'", 1553);
                }

                $alterations[$type][$name] = $alteration->jsonSerialize();
            }

            $remainingAlteration = $this->rules->starting->getNumber($type) - \count($alterations[$type]);
            if ($remainingAlteration < 0) {
                throw new AlterationsApiException("Too much alteration for type '$type'", 1554);
            }

            if ($remainingAlteration > 0) {
                throw new AlterationsApiException("You must choose some other(s) alteration for '$type'", 1555);
            }
        }

        return $alterations;
    }

    public function dump(bool $prettyPrint = false): string
    {
        try {
            return \json_encode($this->rules, flags: \JSON_THROW_ON_ERROR | ($prettyPrint ? \JSON_PRETTY_PRINT : 0));
            // @codeCoverageIgnoreStart
        } catch (\JsonException $exception) {
            throw new AttributesApiException('Unable to dump alterations rules data: ' . $exception->getMessage(), 1451, $exception);
        }
        // @codeCoverageIgnoreEnd
    }
}
