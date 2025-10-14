<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Velkuns\GameTextEngine\Action;

use Random\Randomizer;
use Velkuns\GameTextEngine\Element\Entity\Entity;

readonly class Combat
{
    public function __construct(
        private Randomizer $randomizer,
    ) {}

    /**
     * @return array{hit: bool, damages: int, chance: float, roll: float, debug: array{"hit chance": string, damages: string}}
     */
    public function turn(Entity $attacker, Entity $defender): array
    {
        $attackerModifiers = $attacker->getModifiers($defender);
        $defenderModifiers = $defender->getModifiers($attacker);

        $attack   = $attacker->getAbilities()->get('attack')?->getCurrentWithModifiers($attackerModifiers);
        $strength = $attacker->getAbilities()->get('strength')?->getCurrentWithModifiers($attackerModifiers);

        $defense   = $defender->getAbilities()->get('defense')?->getCurrentWithModifiers($defenderModifiers);
        $endurance = $defender->getAbilities()->get('endurance')?->getCurrentWithModifiers($defenderModifiers);

        if ($attack === null || $defense === null || $strength === null || $endurance === null) {
            throw new \RuntimeException('Both attacker and defender must have attack and defense abilities'); // @codeCoverageIgnore
        }

        //~ hit chance = attack / (defense * 2)
        $hitChance = $attack / ($defense * 2);

        //~ Damages formula: ((attacker strength * 1.5) - defender endurance) + item damages
        $itemDamages = $attacker->getInventory()->getEquippedWeapon()?->getDamages() ?? 0;
        $damages     = (int) floor((($strength * 2) / $endurance) + $itemDamages);

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

    private function inflictDamagesTo(Entity $defender, int $damages): void
    {
        $vitality = $defender->getAbilities()->get('vitality');

        if ($vitality === null) {
            throw new \RuntimeException('Entity has no vitality ability'); // @codeCoverageIgnore
        }

        $vitality->decrease($damages);
    }
}
