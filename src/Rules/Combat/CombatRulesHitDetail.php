<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Velkuns\GameTextEngine\Rules\Combat;

/**
 * @phpstan-type CombatRulesHitDetailData array{name: string, rule: string, description: string}
 */
class CombatRulesHitDetail implements \JsonSerializable
{
    public function __construct(
        public string $name,
        public string $rule,
        public string $description,
    ) {}

    /**
     * @phpstan-return CombatRulesHitDetailData
     */
    public function jsonSerialize(): array
    {
        return [
            'name'        => $this->name,
            'rule'        => $this->rule,
            'description' => $this->description,
        ];
    }
}
