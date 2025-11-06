# Data - Statuses

## Status

`draft`

## Context

To have more complex game mechanics, we can have skills, blessings, curses, physical or magical states that can have
an impact on player (abilities or not).
For example:
- blindness (state) that decrease some abilities related to perception
- a skill that increase the combat abilities
- a benediction that increase the vitality
- a curse that decrease the strength
- ...

List of possible status types:
- skill
- state
- blessing
- curse
- title (purely descriptive, no effect)

## Decision

Status format:
```json
{
    "type": "skill|state|blessing|curse|title",
    "name": "string",
    "description": "string",
    "modifiers": [],
    "durationTurns": "int",
    "remainingTurns": "int"
}
```

Blessing status example:
```json
{
    "type": "blessing",
    "name": "God's Strength",
    "description": "You have been blessed by the god of strength. Your strength is increased by 3.",
    "modifiers": [
        {
            "ability": "strength",
            "value": 3
        }
    ],
    "duration": "int",
    "remaining": "int"
}
```

Curse status example:
```json
{
    "type": "curse",
    "name": "Rat's Weakness",
    "description": "You have been cursed by the rat king. Your endurance is decreased by 2.",
    "modifier": [
        {
            "ability": "endurance",
            "value": -2
        }
    ],
    "durationTurns": "int",
    "remainingTurns": "int"
}
```

State status examples:
```json
{
    "type": "state",
    "name": "Blindness",
    "description": "You are blind. Your intuition is increased by 4, but your agility is decreased by 10.",
    "modifier": [
        {
            "ability": "intuition",
            "value": 4
        },
        {
            "ability": "agility",
            "value": -10
        }
    ],
    "durationTurns": "int",
    "remainingTurns": "int"
}
```

Title status example:
```json
{
    "type": "title",
    "name": "Dragon's Killer",
    "description": "You have slain a dragon. You feel a surge of power, but also a great fatigue.",
    "modifier": []
}
```

Skill status example:
```json

{
    "name": "Sword (Mastery)",
    "type": "skill",
    "description": "Mastering sword techniques increases damage dealt with swords by 2.",
    "modifiers": [
        {
            "combat" : "self.damages.physical.value",
            "value": 2,
            "conditions": {
                "numberRequired": 1,
                "conditions": [
                    {
                        "type": "self.inventory.item",
                        "condition": "subType=sword;equipped=true",
                        "is": true
                    }
                ]
            }
        }
    ]
}
```
