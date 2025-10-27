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
use Velkuns\GameTextEngine\Element\Processor\TimeProcessor;
use Velkuns\GameTextEngine\Utils\Log\CombatLog;

/**
 * @phpstan-type TurnLogData array{player: CombatLog, enemy?: CombatLog}
 */
readonly class Combat
{
    public function __construct(
        private Randomizer $randomizer,
        private TimeProcessor $timeResolver,
    ) {}

    /**
     * @param EntityInterface[] $enemies
     * @return array<int, TurnLogData>
     */
    public function start(EntityInterface $player, array $enemies): array
    {
        $logs = [];
        $turn = 1;
        foreach ($enemies as $enemy) {
            do {
                $logs[$turn] = $this->turn($player, $enemy);

                $this->timeResolver->processTurnOnAll([$player, ...$enemies]);
            } while ($player->isAlive() && $enemy->isAlive());

            if (!$player->isAlive()) {
                //~ stop combat if player is dead
                break; // @codeCoverageIgnore
            }
        }

        return $logs;
    }

    /**
     * @return TurnLogData
     */
    public function turn(EntityInterface $player, EntityInterface $enemy): array
    {
        $turnLogs = [];

        $turnLogs['player'] = $this->tick($player, $enemy);

        if (!$enemy->isAlive()) {
            return $turnLogs; // stop combat with this enemy if it is dead
        }

        $turnLogs['enemy'] = $this->tick($enemy, $player);

        return $turnLogs;
    }

    public function tick(EntityInterface $attacker, EntityInterface $defender): CombatLog
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

        $equippedWeapon = $attacker->getInventory()->getEquippedWeapon();

        //~ Damages formula: ((attacker strength * 1.5) - defender endurance) + item damages
        $itemDamages = $equippedWeapon?->getDamages()?->get('physical')?->getValueWithModifiers($attackerModifiers) ?? 0;
        $damages     = (int) round((($strength * 2) / $endurance) + $itemDamages);

        //~ Roll between 0 and 1 (equidistributed)
        $hitRoll = $this->randomizer->nextFloat();

        $log = new CombatLog(
            $attacker,
            $defender,
            $equippedWeapon,
            $damages,
            $hitChance,
            $hitRoll,
            [
                'hitChance' => "hit chance = $attack / ($defense * 2) = $attack / " . ($defense * 2) . " = $hitChance",
                'damages'   => "damages = (($strength * 2 ) / $endurance) + $itemDamages = " . (($strength * 2) / $endurance) . " + $itemDamages = $damages",
            ],
        );

        //~ Miss
        if ($hitRoll <= $hitChance) {
            //~ Hit
            $this->inflictDamagesTo($defender, $damages);
        }

        return $log;
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
