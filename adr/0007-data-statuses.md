# Data - Traits

## Status

`draft`

## Context

To have more complex game mechanics, we can have skills, blessings, curses, physical or magical states that can have
an impact on player (attributes or not).
For example:
- blindness (state) that decrease some attributes related to perception
- a skill that increase the combat attributes
- a benediction that increase the vitality
- a curse that decrease the strength
- ...

List of possible trait types:
- skill
- state
- blessing
- curse
- title (purely descriptive, no effect)

## Decision

Trait format:
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

Blessing trait example:
```json
{
    "type": "blessing",
    "name": "God's Strength",
    "description": "You have been blessed by the god of strength. Your strength is increased by 3.",
    "modifiers": [
        {
            "attribute": "strength",
            "value": 3
        }
    ],
    "duration": "int",
    "remaining": "int"
}
```

Curse trait example:
```json
{
    "type": "curse",
    "name": "Rat's Weakness",
    "description": "You have been cursed by the rat king. Your endurance is decreased by 2.",
    "modifier": [
        {
            "attribute": "endurance",
            "value": -2
        }
    ],
    "durationTurns": "int",
    "remainingTurns": "int"
}
```

State trait examples:
```json
{
    "type": "state",
    "name": "Blindness",
    "description": "You are blind. Your intuition is increased by 4, but your agility is decreased by 10.",
    "modifier": [
        {
            "attribute": "intuition",
            "value": 4
        },
        {
            "attribute": "agility",
            "value": -10
        }
    ],
    "durationTurns": "int",
    "remainingTurns": "int"
}
```

Title trait example:
```json
{
    "type": "title",
    "name": "Dragon's Killer",
    "description": "You have slain a dragon. You feel a surge of power, but also a great fatigue.",
    "modifier": []
}
```

Skill trait example:
```json

{
    "name": "Sword (Mastery)",
    "type": "skill",
    "description": "Mastering sword techniques increases damage dealt with swords by 2.",
    "modifiers": [
        {
            "combat" : "self.damages.physical.value",
            "value": 2,
            "prerequisites": {
                "numberRequired": 1,
                "requirements": [
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
