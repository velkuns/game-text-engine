<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Velkuns\GameTextEngine\Api;

use Random\Randomizer;
use Velkuns\GameTextEngine\Element\Entity\EntityInterface;

/**
 * @phpstan-type TurnLogData array{hit: bool, damages: int, chance: float, roll: float, debug: array{"hit chance": string, damages: string}}
 */
readonly class Combat
{
    public function __construct(
        private Randomizer $randomizer,
    ) {}

    /**
     * @param EntityInterface[] $enemies
     * @return array<int, array{0: TurnLogData, 1?: TurnLogData}>
     */
    public function start(EntityInterface $player, array $enemies): array
    {
        $logs = [];
        $turn = 1;
        foreach ($enemies as $enemy) {
            do {
                $logs[$turn][0] = $this->turn($player, $enemy);
                if (!$enemy->isAlive()) {
                    break; // stop combat with this enemy if it is dead
                }

                $logs[$turn][1] = $this->turn($enemy, $player);
            } while ($player->isAlive());

            if (!$player->isAlive()) {
                //~ stop combat if player is dead
                break; // @codeCoverageIgnore
            }
        }

        return $logs;
    }

    /**
     * @return array{hit: bool, damages: int, chance: float, roll: float, debug: array{"hit chance": string, damages: string}}
     */
    public function turn(EntityInterface $attacker, EntityInterface $defender): array
    {
        $attackerModifiers = $attacker->getModifiers($defender);
        $defenderModifiers = $defender->getModifiers($attacker);

        $attack   = $attacker->getAbilities()->get('attack')?->getValueWithModifiers($attackerModifiers);
        $strength = $attacker->getAbilities()->get('strength')?->getValueWithModifiers($attackerModifiers);

        $defense   = $defender->getAbilities()->get('defense')?->getValueWithModifiers($defenderModifiers);
        $endurance = $defender->getAbilities()->get('endurance')?->getValueWithModifiers($defenderModifiers);

        if ($attack === null || $defense === null || $strength === null || $endurance === null) {
            throw new \UnexpectedValueException('Both attacker and defender must have attack and defense abilities'); // @codeCoverageIgnore
        }

        //~ hit chance = attack / (defense * 2)
        $hitChance = $attack / ($defense * 2);

        //~ Damages formula: ((attacker strength * 1.5) - defender endurance) + item damages
        $itemDamages = $attacker->getInventory()->getEquippedWeapon()?->getDamages() ?? 0;
        $damages     = (int) round((($strength * 2) / $endurance) + $itemDamages);

        //~ Roll between 0 and 1 (equidistributed)
        $hitRoll = $this->randomizer->nextFloat();

        $result = [
            'hit'     => false,
            'damages' => $damages,
            'chance'  => $hitChance,
            'roll'    => $hitRoll,
            'debug' => [
                'hit chance' => "hit chance = $attack / ($defense * 2) = $attack / " . ($defense * 2) . " = $hitChance",
                'damages'    => "damages = (($strength * 2 ) / $endurance) + $itemDamages = " . (($strength * 2) / $endurance) . " + $itemDamages = $damages",
            ],
        ];

        //~ Miss
        if ($hitRoll > $hitChance) {
            return $result;
        }

        //~ Hit
        $this->inflictDamagesTo($defender, $damages);

        $result['hit'] = true;

        return $result;
    }

    private function inflictDamagesTo(EntityInterface $defender, int $damages): void
    {
        $vitality = $defender->getAbilities()->get('vitality');

        if ($vitality === null) {
            throw new \UnexpectedValueException('Entity has no vitality ability'); // @codeCoverageIgnore
        }

        $vitality->decrease($damages);
    }
}
