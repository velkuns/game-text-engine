<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Tests\Integration\Api;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Velkuns\GameTextEngine\Exception\Api\PlayerApiException;
use Velkuns\GameTextEngine\Exception\Rules\AbilitiesRulesException;
use Velkuns\GameTextEngine\Exception\Rules\PlayerRulesException;
use Velkuns\GameTextEngine\Exception\Rules\StatusesRulesException;
use Velkuns\GameTextEngine\Rpg\Item\ItemInterface;
use Velkuns\GameTextEngine\Tests\Helper\ApiTrait;
use Velkuns\GameTextEngine\Tests\Helper\EntityTrait;
use Velkuns\GameTextEngine\Tests\Helper\FactoryTrait;

/**
 * @phpstan-import-type ItemData from ItemInterface
 */
class PlayerApiTest extends TestCase
{
    use ApiTrait;
    use EntityTrait;
    use FactoryTrait;

    public function testNew(): void
    {
        $playerApi = self::getPlayerApi();

        $newPlayerData = [
            'name'        => 'New Hero',
            'age'         => 25,
            'race'        => 'elf',
            'gender'      => 'non-binary',
            'description' => 'A brave hero',
            'background'  => 'Born in a small village',
            'abilities' => [
                'strength'  => 11,
                'endurance' => 12,
                'agility'   => 13,
                'intuition' => 14,
            ],
            'inventory' => ['Rusty Sword'],
        ];

        $expected = self::getPlayer(true);

        $playerApi->new($newPlayerData);

        self::assertEquals($expected, $playerApi->player);
    }

    public function testConsume(): void
    {
        $playerApi = self::getPlayerApi();

        $newPlayerData = [
            'name'        => 'New Hero',
            'age'         => 25,
            'race'        => 'elf',
            'gender'      => 'non-binary',
            'description' => 'A brave hero',
            'background'  => 'Born in a small village',
            'abilities' => [
                'strength'  => 11,
                'endurance' => 12,
                'agility'   => 13,
                'intuition' => 14,
            ],
            'inventory' => ['Rusty Sword', 'Small Health Potion'],
        ];

        $playerApi->new($newPlayerData);
        self::assertSame(23, $playerApi->player->getAbilities()->get('vitality')?->getValue());

        $playerApi->player->getInventory()->get('Small Health Potion')?->setQuantity(2);
        $playerApi->consume('Small Health Potion'); // (11 + 12) +5 vitality => should be 28

        self::assertSame(28, $playerApi->player->getAbilities()->get('vitality')->getValue());
        self::assertSame(1, $playerApi->player->getInventory()->get('Small Health Potion')?->getQuantity());

        $playerApi->consume('Small Health Potion');

        self::assertSame(33, $playerApi->player->getAbilities()->get('vitality')->getValue());
        self::assertNull($playerApi->player->getInventory()->get('Small Health Potion'));
    }

    public function testConsumeButItemNotInInventory(): void
    {
        $playerApi = self::getPlayerApi();

        $newPlayerData = [
            'name'        => 'New Hero',
            'age'         => 25,
            'race'        => 'elf',
            'gender'      => 'non-binary',
            'description' => 'A brave hero',
            'background'  => 'Born in a small village',
            'abilities' => [
                'strength'  => 11,
                'endurance' => 12,
                'agility'   => 13,
                'intuition' => 14,
            ],
            'inventory' => ['Rusty Sword', 'Small Health Potion'],
        ];

        $playerApi->new($newPlayerData);

        self::expectException(PlayerApiException::class);
        self::expectExceptionCode(1410);
        $playerApi->consume('Medium Health Potion');
    }

    public function testConsumeButItemIsNotConsumable(): void
    {
        $playerApi = self::getPlayerApi();

        $newPlayerData = [
            'name'        => 'New Hero',
            'age'         => 25,
            'race'        => 'elf',
            'gender'      => 'non-binary',
            'description' => 'A brave hero',
            'background'  => 'Born in a small village',
            'abilities' => [
                'strength'  => 11,
                'endurance' => 12,
                'agility'   => 13,
                'intuition' => 14,
            ],
            'inventory' => ['Rusty Sword', 'Small Health Potion'],
        ];

        $playerApi->new($newPlayerData);

        self::expectException(PlayerApiException::class);
        self::expectExceptionCode(1411);
        $playerApi->consume('Rusty Sword');
    }

    public function testLevelUp(): void
    {
        $playerApi = self::getPlayerApi();
        $playerApi->player->getInfo()->level = 2;
        $playerApi->player->getInfo()->xp    = 220;

        $abilities = [
            'strength'  => 1,
            'endurance' => 1,
            'agility'   => 1,
            'intuition' => 1,
            'vitality'  => 1,
        ];

        self::assertSame(2, $playerApi->player->getInfo()->level);
        self::assertSame(220, $playerApi->player->getInfo()->xp);
        self::assertSame(23, $playerApi->player->getAbilities()->get('vitality')?->getValue());
        self::assertNull($playerApi->player->getStatuses()->getByType('skill', 'Archery (Mastery)')?->getName());

        $playerApi->levelUp($abilities, ['skill' => ['Archery (Mastery)']]);

        self::assertSame(3, $playerApi->player->getInfo()->level);
        self::assertSame(20, $playerApi->player->getInfo()->xp);
        self::assertSame('Archery (Mastery)', $playerApi->player->getStatuses()->getByType('skill', 'Archery (Mastery)')?->getName());
        self::assertSame(24, $playerApi->player->getAbilities()->get('vitality')->getValue());
    }

    /**
     * @param array<string, int> $abilities
     * @param array<string, list<string>> $statuses
     * @param class-string<\Throwable> $exception
     */
    #[DataProvider('getLevelUpDataProvider')]
    public function testLevelUpButHaveError(
        int $level,
        int $xp,
        array $abilities,
        array $statuses,
        string $exception,
        string $message,
        int $code,
    ): void {
        $playerApi = self::getPlayerApi();
        $playerApi->player->getInfo()->level = $level;
        $playerApi->player->getInfo()->xp    = $xp;

        self::expectException($exception);
        self::expectExceptionMessage($message);
        self::expectExceptionCode($code);
        $playerApi->levelUp($abilities, $statuses);
    }

    /**
     * @return array<string, array{
     *     level: int,
     *     abilities: array<string, int>,
     *     statuses: array<string, list<string>>,
     *     exception: class-string<\Throwable>,
     *     message: string,
     *     code: int,
     * }>
     */
    public static function getLevelUpDataProvider(): array
    {
        return [
            'max level already reached' => [
                'level'     => 10,
                'xp'        => 1000,
                'abilities' => ['strength' => 1, 'endurance' => 1, 'agility' => 1, 'intuition' => 1, 'vitality' => 1],
                'statuses'  => ['skill' => ['Archery (Mastery)']],
                'exception' => PlayerRulesException::class,
                'message'   => 'You already reached the max level (10)',
                'code'      => 2300,
            ],
            'Not enough xp to level up' => [
                'level'     => 1,
                'xp'        => 95,
                'abilities' => ['strength' => 1, 'endurance' => 1, 'agility' => 1, 'intuition' => 1, 'vitality' => 1],
                'statuses'  => ['skill' => ['Archery (Mastery)']],
                'exception' => PlayerRulesException::class,
                'message'   => "You need 5 XP to reach the next level.",
                'code'      => 2301,
            ],
            'to much abilities points for one ability' => [
                'level'     => 1,
                'xp'        => 100,
                'abilities' => ['strength' => 3, 'endurance' => 0, 'agility' => 0, 'intuition' => 1, 'vitality' => 1],
                'statuses'  => ['skill' => ['Archery (Mastery)']],
                'exception' => AbilitiesRulesException::class,
                'message'   => "Only 2 point(s) per ability is allowed for level up.",
                'code'      => 2100,
            ],
            'to much abilities points in total' => [
                'level'     => 1,
                'xp'        => 100,
                'abilities' => ['strength' => 2, 'endurance' => 1, 'agility' => 1, 'intuition' => 1, 'vitality' => 1],
                'statuses'  => ['skill' => ['Archery (Mastery)']],
                'exception' => AbilitiesRulesException::class,
                'message'   => "Only 5 point(s) is allowed for level up.",
                'code'      => 2101,
            ],
            'remaining points to attribute' => [
                'level'     => 1,
                'xp'        => 100,
                'abilities' => ['strength' => 1, 'endurance' => 0, 'agility' => 0, 'intuition' => 1, 'vitality' => 1],
                'statuses'  => ['skill' => ['Archery (Mastery)']],
                'exception' => AbilitiesRulesException::class,
                'message'   => "Remaining 2 point(s) to attribute to abilities",
                'code'      => 2102,
            ],
            'new status not allowed on this level' => [
                'level'     => 1,
                'xp'        => 100,
                'abilities' => ['strength' => 1, 'endurance' => 1, 'agility' => 1, 'intuition' => 1, 'vitality' => 1],
                'statuses'  => ['skill' => ['Archery (Mastery)']],
                'exception' => StatusesRulesException::class,
                'message'   => "New statuses only allowed every 2 level(s).",
                'code'      => 2200,
            ],
            'no attribution allowed of this kind of status' => [
                'level'     => 2,
                'xp'        => 200,
                'abilities' => ['strength' => 1, 'endurance' => 1, 'agility' => 1, 'intuition' => 1, 'vitality' => 1],
                'statuses'  => ['skill' => ['Archery (Mastery)'], 'state' => ['poisoned']],
                'exception' => StatusesRulesException::class,
                'message'   => "Only 0 'state' allowed, 1 given.",
                'code'      => 2201,
            ],
            'remaining status of this kind to attribute' => [
                'level'     => 2,
                'xp'        => 200,
                'abilities' => ['strength' => 1, 'endurance' => 1, 'agility' => 1, 'intuition' => 1, 'vitality' => 1],
                'statuses'  => [],
                'exception' => StatusesRulesException::class,
                'message'   => "Remaining 1 status(es) of 'skill' to attribute.",
                'code'      => 2202,
            ],
            'status not found' => [
                'level'     => 2,
                'xp'        => 200,
                'abilities' => ['strength' => 1, 'endurance' => 1, 'agility' => 1, 'intuition' => 1, 'vitality' => 1],
                'statuses'  => ['skill' => ['Archery (Not Exists)']],
                'exception' => StatusesRulesException::class,
                'message'   => "Status 'Archery (Not Exists)' of type 'skill' not found.",
                'code'      => 2000,
            ],
        ];
    }
}
