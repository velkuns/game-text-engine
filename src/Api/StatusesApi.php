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
use Velkuns\GameTextEngine\Api\Exception\StatusesApiException;
use Velkuns\GameTextEngine\Element\Entity\EntityStatuses;
use Velkuns\GameTextEngine\Element\Factory\StatusFactory;
use Velkuns\GameTextEngine\Element\Status\StatusInterface;

/**
 * @phpstan-import-type StatusesData from EntityStatuses
 * @phpstan-import-type StatusData from StatusInterface
 * @phpstan-type StatusesRulesData array{
 *     description: string,
 *     attributions: array<string, int>,
 *     statuses: array<string, list<StatusData>>,
 * }
 */
class StatusesApi
{
    public string $description = '';

    /** @var array<string, int> $attributions */
    public array $attributions = [];

    /** @var array<string, array<string, StatusInterface>> $statuses */
    public array $statuses = [];

    public function __construct(
        private readonly StatusFactory $statusFactory,
    ) {}

    /**
     * @phpstan-param StatusesRulesData $data
     */
    public function load(array $data): void
    {
        $this->description  = $data['description'];
        $this->attributions = $data['attributions'];

        foreach ($data['statuses'] as $type => $list) {
            $this->statuses[$type] = [];
            foreach ($list as $statusData) {
                $status = $this->statusFactory->from($statusData);
                $this->statuses[$type][$status->getName()] = $status;
            }
        }
    }

    /**
     * @return array<string, array<string, StatusInterface>>
     */
    public function getAll(): array
    {
        return $this->statuses;
    }

    public function get(string $type, string $name, bool $asClone = true): ?StatusInterface
    {
        $status = $this->statuses[$type][$name] ?? null;

        return $asClone ? $status?->clone() : $status;
    }

    public function set(StatusInterface $status): self
    {
        if (!isset($this->statuses[$status->getType()])) {
            throw new StatusesApiException("Unknown status type '{$status->getType()}'", 1550);
        }

        $this->statuses[$status->getType()][$status->getName()] = $status;

        return $this;
    }

    public function remove(string $type, string $name): self
    {
        if (!isset($this->statuses[$type][$name])) {
            throw new StatusesApiException("The status '$name' with type '$type' does not exist.", 1551);
        }

        unset($this->statuses[$type][$name]);

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
        foreach ($this->attributions as $type => $value) {
            $statuses[$type] = [];
        }

        //~ Transform abilities into data and separate bases with ou without init rule.
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

            $remainingStatus = $this->attributions[$type] - \count($statuses[$type]);
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
            $data = [
                'description'  => $this->description,
                'attributions' => $this->attributions,
                'statuses'     => \array_map(fn(array $list) => \array_values($list), $this->statuses),
            ];

            return \json_encode($data, flags: \JSON_THROW_ON_ERROR | ($prettyPrint ? \JSON_PRETTY_PRINT : 0));
            // @codeCoverageIgnoreStart
        } catch (\JsonException $exception) {
            throw new AbilitiesApiException('Unable to dump statuses rules data: ' . $exception->getMessage(), 1451, $exception);
        }
        // @codeCoverageIgnoreEnd
    }
}
