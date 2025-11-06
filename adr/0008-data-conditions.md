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
- self.ability: check an ability value
- self.statuses.skill: check if a skill is present or not
- self.statuses.state: check if a state is present or not
- self.statuses.blessing: check if a blessing is present or not
- self.statuses.curse: check if a curse is present or not
- self.statuses.title: check if a title is present or not
- self.inventory.item: check if an item is present or not (in inventory or equipped)
- enemy.info: check for enemy information value (like race or size)

## Decision

Condition format:

```json
{
    "number": "int",
    "conditions": [
        {
            "from": "string",
            "condition": "string",
            "is": "bool"
        }
    ]
}
```

condition node format:
```
"element=value"
```

if the condition must meet multiple criteria, we can use:
```
"element1=value1,element2=value2"
```

### Operators
List of supported operators are:
- `=`
- `!=`
- `>`
- `<`
- `>=`
- `<=`
- `&` (when condition value is a flag bitmask)

### Some examples
#### Size of the entity (player or enemy)
```json
{
    "type": "self.ability.strength",
    "condition": "value>5",
    "is": true
}
```
#### Skill presence
```json
{
    "type": "self.statuses.skills",
    "condition": "name=lockpicking",
    "is": true
}
```
#### Size of the entity (player or enemy)
```json
{
    "type": "enemy.info",
    "condition": "size=tiny",
    "is": true
}
```

#### Race of the entity
```json
{
   "type": "enemy.info",
   "condition": "race=goblin",
   "is": true
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
        "numberRequired": 1,
        "conditions": [
            {
               "type": "self.ability.strength",
               "condition": "value>6",
               "is": true
            },
            {
               "type": "self.ability.endurance",
               "condition": "value>=5",
               "is": true
            }
        ]
    }
}
```

State condition example:

```json
{
   "conditions": {
      "numberRequired": 3,
      "conditions": [
         {
            "type": "self.statuses.skills",
            "condition": "name=lockpicking",
            "is": true
         },
         {
            "type": "self.statuses.states",
            "condition": "name=blindness",
            "is": false
         },
         {
            "type": "self.ability.agility",
            "condition": "value>=6",
            "is": true
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
        "numberRequired": 1,
        "conditions": [
            {
                "type": "self.inventory.item",
                "condition": "subType=axe",
                "is": true
            }
        ]
    }
}
```

Weapon condition example:
- Must have at least 1 weapon equipped item to attack.

```json
{
    "conditions": {
        "numberRequired": "1",
        "conditions": [
            {
                "type": "self.inventory.item",
                "condition": "flags&4;equipped=true",
                "is": true
            }
        ]
    }
}
```
> - Flags: 4 = item can be used as a weapon
