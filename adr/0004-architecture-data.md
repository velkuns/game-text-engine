# Architecture - Data

## Status

`draft`

## Context

Text Game Engine need to manipulate data, like:
- player characteristics
- inventory
- places
- NPCs
- items
- monsters

But the this game engine is agnostic of the data storage. So we need to define a way to "normalize" data structures,
that allow the system to "serialize" and "unserialize" the data, and build PHP objects based on those data.

## Decision

One way to achieve this is to `serialize` data in `json` format. The data serialized will be a valid json that contains
all required information to be parsed and converted into PHP objects directly from the Text Game Engine itself.

In that way, the application / game that will use the Text Game Engine will only need to store and retrieve data in
storage of it's' choice (database, files, etc...), and provide the data in json format to the Text Game Engine.

Also to avoid complexity in app/game side, the data will not be split into many part if they work together.
For example, a player will have all his characteristics in one json blob, instead of having one json blob for strength,
agility, etc... This will make it easier to manage data in the app/game side.

## Consequences

Pros:
- The game engine still agnostic of the data storage
- The game engine is responsible to `serialize` and `unserialize` data.
- All the logic of parsing or manipulating data is under the responsibility of the Text Game Engine

Cons:
- On the app/game side, each type of data become a `string` (serialized data), and don't have control on data directly


### Player data structure
Format:
```json
{
    "name": "string",
    "info": {
        "description": "string",
        "background": "string",
        "size": "int",
        "age": "int"
    },
    "notes": "string",
    "inventory": [
        {
            "item_id": "int",
            "quantity": "int",
            "notes": "string"
        }
    ],
    "abilities": {
        "ability_name#1": {
            "type": "base|compound",
            "init?": "string",
            "value?": "int",
            "initial?": "int",
            "range?": {
                "min": "int",
                "max": "int"
            },
            "rule?": "string"
        }
    }
}
```
Example:
```json
{
    "name": "player #1",
    "info": {
        "description": "...",
        "background": "...",
        "size": "1m75",
        "age": "20"
    },
    "notes": "...",
    "inventory": [
        {
            "name": "..."
        },
        {
            "item_id": 2,
            "quantity": 3,
            "notes": "..."
        }
    ],
    "abilities": {
        "strength": {
            "type": "base",
            "value": 1,
            "initial": 6,
            "range": {
                "min": 1,
                "max": 12
            }
        },
        "endurance": {
            "type": "base",
            "value": 2,
            "initial": 6,
            "range": {
                "min": 1,
                "max": 12
            }
        },
        "agility": {
            "type": "base",
            "value": 3,
            "initial": 6,
            "range": {
                "min": 1,
                "max": 12
            }
        },
        "intuition": {
            "type": "base",
            "value": 4,
            "initial": 6,
            "range": {
                "min": 1,
                "max": 12
            }
        },
        "vitality": {
            "type": "base",
            "init": "strength + endurance",
            "value": 3,
            "initial": 12,
            "range": {
                "min": 1,
                "max": 12
            }
        },
        "attack": {
            "type": "compound",
            "rule": "(strength + agility) / 2"
        },
        "defense": {
            "type": "compound",
            "rule": "((endurance + intuition) * 2 / 2 )+ 4"
        }
    }
}
```

### Item data structure
Item flags (bit value):
- base: 0 (base for all items)
- consumable: 1 (can be consumed/used)
- weapon: 2 (can be used to attack)
- shield: 4 (can be used a shield to protect and block attack)
- gear: 8 (can be equipped in gear slot)
- throwable: 16 (knife, "splash potion", etc...)
- left_hand: 32 (can be equipped in left hand)
- right_hand: 64 (can be equipped in right hand)

```json
{
    "name": "string",
    "description": "string",
    "modifiers": "list<Modifier>",
    "flags": "int",
    "isEquipped": "bool",
    "price": "int"
}
```

### Ability data structure
```json
{
    "name": "string",
    "type": "base|compound",
    "initial": "int",
    "current": "int",
    "max": "int",
    "rule": "string|null"
}
```

### Modifier data structure
```json
{
    "name": "string",
    "ability": "string",
    "value": "int",
    "description": "string"
}
```
