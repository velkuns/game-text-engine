<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Api;

use Velkuns\GameTextEngine\Core\Factory\StatusFactory;
use Velkuns\GameTextEngine\Exception\Api\AttributesApiException;
use Velkuns\GameTextEngine\Exception\Api\StatusesApiException;
use Velkuns\GameTextEngine\Rpg\Entity\EntityStatuses;
use Velkuns\GameTextEngine\Rpg\Status\StatusInterface;
use Velkuns\GameTextEngine\Rules\Statuses\StatusesRules;
use Velkuns\GameTextEngine\Rules\Statuses\StatusesRulesLeveling;
use Velkuns\GameTextEngine\Rules\Statuses\StatusesRulesStarting;

/**
 * @phpstan-import-type StatusesData from EntityStatuses
 * @phpstan-import-type StatusData from StatusInterface
 * @phpstan-import-type StatusesRulesData from StatusesRules
 */
class StatusesApi
{
    public StatusesRules $rules;

    public function __construct(
        private readonly StatusFactory $statusFactory,
    ) {}

    /**
     * @phpstan-param StatusesRulesData $data
     */
    public function load(array $data): void
    {
        $description = $data['description'];
        $starting    = new StatusesRulesStarting(...$data['starting']);
        $leveling    = new StatusesRulesLeveling(...$data['leveling']);

        $statuses = [];
        foreach ($data['statuses'] as $type => $list) {
            $statuses[$type] = [];
            foreach ($list as $statusData) {
                $status = $this->statusFactory->from($statusData);
                $statuses[$type][$status->getName()] = $status;
            }
        }

        $this->rules = new StatusesRules($description, $starting, $leveling, $statuses);
    }

    /**
     * @return array<string, array<string, StatusInterface>>
     */
    public function getAll(): array
    {
        return $this->rules->statuses;
    }

    public function get(string $type, string $name, bool $asClone = true): ?StatusInterface
    {
        $status = $this->rules->statuses[$type][$name] ?? null;

        return $asClone ? $status?->clone() : $status;
    }

    public function set(StatusInterface $status): self
    {
        if (!isset($this->rules->statuses[$status->getType()])) {
            throw new StatusesApiException("Unknown status type '{$status->getType()}'", 1550);
        }

        $this->rules->statuses[$status->getType()][$status->getName()] = $status;

        return $this;
    }

    public function remove(string $type, string $name): self
    {
        if (!isset($this->rules->statuses[$type][$name])) {
            throw new StatusesApiException("The status '$name' with type '$type' does not exist.", 1551);
        }

        unset($this->rules->statuses[$type][$name]);

        return $this;
    }

    /**
     * @param array<string, list<string>> $data
     * @phpstan-return StatusesData
     */
    public function fromNewPlayer(array $data): array
    {
        $statuses = [];

        //~ Pre-build $statuses
        foreach ($this->rules->starting->attributions as $type => $value) {
            $statuses[$type] = [];
        }

        //~ Transform attributes into data and separate bases with ou without init rule.
        foreach ($data as $type => $list) {
            if (!isset($statuses[$type])) {
                throw new StatusesApiException("Unknown status '$type'", 1552);
            }

            foreach ($list as $name) {
                $status = $this->get($type, $name, false);
                if ($status === null) {
                    throw new StatusesApiException("Unknown status '$name' with type '$type'", 1553);
                }

                $statuses[$type][$name] = $status->jsonSerialize();
            }

            $remainingStatus = $this->rules->starting->attributions[$type] - \count($statuses[$type]);
            if ($remainingStatus < 0) {
                throw new StatusesApiException("Too much status for type '$type'", 1554);
            }

            if ($remainingStatus > 0) {
                throw new StatusesApiException("You must choose some other(s) status for '$type'", 1555);
            }
        }

        return $statuses;
    }

    public function dump(bool $prettyPrint = false): string
    {
        try {
            return \json_encode($this->rules, flags: \JSON_THROW_ON_ERROR | ($prettyPrint ? \JSON_PRETTY_PRINT : 0));
            // @codeCoverageIgnoreStart
        } catch (\JsonException $exception) {
            throw new AttributesApiException('Unable to dump statuses rules data: ' . $exception->getMessage(), 1451, $exception);
        }
        // @codeCoverageIgnoreEnd
    }
}
