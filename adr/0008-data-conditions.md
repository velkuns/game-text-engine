# Data - Conditions

## Status

`draft`

## Context

In some case, state or action require some conditions to be applied.
And we can have multiple conditions to be applied.
So we need to define number of conditions required from a list to be considered as met, and the list of those conditions.

- number: number of conditions required to be met
- list: list of conditions

Condition types:
- ability: check an ability value
- skill: check if a skill is present or not
- state: check if a state is present or not
- blessing: check if a blessing is present or not
- curse: check if a curse is present or not
- title: check if a title is present or not
- item: check if an item is present or not (in inventory or equipped)
- entity_size: check size of the entity (player or enemy)
- entity_race: check race of the entity (player or enemy)

## Decision

Condition format:

```json
{
    "number": "int",
    "list": [
        {
            "type": "string",
            "operator": ">|<|>=|<=|=|!=",
            "value": "int",
            "name?": "string",
            "subtype?": "string",
            "equipped?": "boolean",
            "flags?": "int"
        }
    ]
}
```

### Ability conditions type
```json
{
    "type": "ability",
    "name": "string",
    "operator": ">|<|>=|<=|=|!=",
    "value": "int"
}
```

### State conditions type
```json
{
    "type": "skill|state|blessing|curse|title",
    "name": "string",
    "operator": "=",
    "value": "0|1"
}
```
> Value is 1 if the state must be present, 0 if it must not be present.

### Item conditions type
```json
{
    "type": "item",
    "name": "string (can be empty)",
    "operator": "=",
    "value": "0|1",
    "equipped?": "boolean",
    "flags?": "int",
    "subtype?": "string"
}
```
> Value is 1 if the item must be present, 0 if it must not be present.
> Equipped and flags are optional and can be used to match more precisely the condition.

### Entities conditions type
#### Size of the entity (player or enemy)
```json
{
    "type": "entity_size",
    "name": "tiny|small|medium|large|huge",
    "operator": "=",
    "value": "0|1"
}
```

#### Race of the entity (player or enemy)
```json
{
    "type": "entity_race",
    "name": "rat|goblin|human|elf|dwarf|orc|troll|...",
    "operator": "=",
    "value": "0|1"
}
```






### Examples
Ability condition example:
 - At least 1 of the following conditions must be met:
   - strength > 6
   - endurance >= 5

```json
{
    "conditions": {
        "numberRequired": "1",
        "list": [
            {
                "type": "ability",
                "name": "strength",
                "operator": ">",
                "value": 6
            },
            {
                "type": "ability",
                "name": "endurance",
                "operator": ">=",
                "value": 5
            }
        ]
    }
}
```

State condition example:

```json
{
    "conditions": {
        "numberRequired": "3",
        "list": [
            {
                "type": "skill",
                "name": "lockpicking",
                "operator": "=",
                "value": 1
            },
            {
                "type": "state",
                "name": "blindness",
                "operator": "=",
                "value": 0
            },
            {
                "type": "ability",
                "name": "agility",
                "operator": ">=",
                "value": 8
            }
        ]
    }
}
```

Weapon condition example:
- Must have at least 1 axe in inventory to chop a tree.

```json
{
    "conditions": {
        "numberRequired": "1",
        "list": [
            {
                "type": "item",
                "name": "",
                "operator": "=",
                "value": 1,
                "subtype": "axe"
            }
        ]
    }
}
```

Weapon condition example:
- Must have at least 1 item that can be used as a weapon in inventory to break a simple closed locked door.

```json
{
    "conditions": {
        "numberRequired": "1",
        "list": [
            {
                "type": "item",
                "name": "",
                "operator": "=",
                "value": 1,
                "flags": 4
            }
        ]
    }
}
```
> - Flags: 4 = item can be used as a weapon
