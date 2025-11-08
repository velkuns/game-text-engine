<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Integration\Utils\Loader;

use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Core\Loader\JsonLoader;
use Velkuns\GameTextEngine\Exception\Core\LoaderException;
use Velkuns\GameTextEngine\Rpg\Item\ItemInterface;

/**
 * @phpstan-import-type ItemData from ItemInterface
 */
class JsonLoaderTest extends TestCase
{
    public function testLoadFromFile(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../../data');
        $loader  = new JsonLoader();

        /** @var list<ItemData> $itemsData */
        $itemsData = $loader->fromFile($dataDir . '/items.json');

        $item = $itemsData[0];
        self::assertSame('item', $item['type']);
        self::assertSame('Rusty Sword', $item['name']);
        self::assertSame('sword', $item['subType']);
        self::assertSame('A rusty sword', $item['description']);
        self::assertSame([], $item['modifiers']);
        self::assertSame(6, $item['flags']);
        self::assertFalse($item['equipped']);
        self::assertSame(['physical' => ['type' => 'physical', 'value' => 3]], $item['damages']);
        self::assertSame(0, $item['price']);
    }

    public function testLoadFromFileWhenFileDoesNotExists(): void
    {
        $dataDir = (string) realpath(__DIR__ . '/../../../../data');
        $loader  = new JsonLoader();

        self::expectException(LoaderException::class);
        self::expectExceptionCode(1200);
        $loader->fromFile($dataDir . '/not-exists.json');
    }

    public function testLoadFromStringWhenJsonIsInvalid(): void
    {
        $loader  = new JsonLoader();

        self::expectException(LoaderException::class);
        self::expectExceptionCode(1203);
        $loader->fromString('{"invalidJson": ');
    }
}
