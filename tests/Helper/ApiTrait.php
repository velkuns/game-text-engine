<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Helper;

use Velkuns\GameTextEngine\Api\Bestiary;
use Velkuns\GameTextEngine\Api\Items;
use Velkuns\GameTextEngine\Element\Item\ItemInterface;
use Velkuns\GameTextEngine\Utils\Loader\JsonLoader;

/**
 * @phpstan-import-type BestiaryData from Bestiary
 * @phpstan-import-type ItemData from ItemInterface
 */
trait ApiTrait
{
    use FactoryTrait;

    private static ?Bestiary $bestiary = null;
    private static ?Items $items = null;

    private static function getBestiary(): Bestiary
    {
        if (self::$bestiary === null) {
            self::$bestiary = new Bestiary(self::getEntityFactory(), self::getItems());

            /** @var list<BestiaryData> $data */
            $data = (new JsonLoader())->fromFile(__DIR__ . '/../../data/bestiary.json');
            self::$bestiary->load($data);
        }

        return self::$bestiary;
    }


    private static function getItems(): Items
    {
        if (self::$items === null) {
            self::$items = new Items(self::getItemFactory());

            /** @var list<ItemData> $data */
            $data = (new JsonLoader())->fromFile(__DIR__ . '/../../data/items.json');
            self::$items->load($data);
        }

        return self::$items;
    }
}
