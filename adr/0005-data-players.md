# Data - Players

## Status

`draft`

## Context

Need to define & store some player basic information, like:
- name
- description
- background
- size (in cm)
- age (in years)
- notes
- inventory (list of items with quantity & notes)
- abilities (list of abilities, base & compound)


## Decision

So, the format will be:
Format:
```json
{
    "name": "string",
    "info": {
        "size": "int",
        "age": "int",
        "race": "string",
        "description": "string",
        "background": "string"
    },
    "notes": "string",
    "inventory": [],
    "abilities": {}
}
```

Example:
```json
{
    "name": "player #1",
    "info": {
        "size": "175",
        "age": "20",
        "race": "human",
        "description": "...",
        "background": "..."
    },
    "notes": "...",
    "inventory": [],
    "abilities": {
        "strength": {},
        "endurance": {},
        "agility": {},
        "intuition": {},
        "vitality": {},
        "attack": {
            "type": "compound",
            "rule": "(strength + agility) / 2"
        },
        "defense": {
            "type": "compound",
            "rule": "(((endurance + intuition) * 2) / 2 ) + 4"
        }
    }
}
```
