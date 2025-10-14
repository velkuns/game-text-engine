<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Element\Entity;

use Velkuns\GameTextEngine\Element\Item\ItemInterface;
use Velkuns\GameTextEngine\Element\Status\StatusType;

/**
 * @phpstan-import-type EntityInfoData from EntityInfo
 * @phpstan-import-type AbilitiesData from EntityAbilities
 * @phpstan-import-type StatusesData from EntityStatuses
 * @phpstan-import-type ItemData from ItemInterface
 * @phpstan-type EntityData array{
 *     name: string,
 *     type: string,
 *     coins: int,
 *     info: EntityInfoData,
 *     abilities: AbilitiesData,
 *     statuses: StatusesData,
 *     inventory: list<ItemData>,
 * }
 */
interface EntityInterface extends \JsonSerializable
{
    public function getName(): string;

    public function getType(): EntityType;

    public function getCoins(): int;

    public function getInfo(): EntityInfo;

    public function getAbilities(): EntityAbilities;

    public function getStatuses(): EntityStatuses;

    public function getInventory(): EntityInventory;

    public function hasStatus(StatusType $statusType, string $statusName): bool;
}
