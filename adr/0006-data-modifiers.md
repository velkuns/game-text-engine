# Data - Modifiers

## Status

`draft`

## Context

When we have some magical items or effects that can modify the value of an attribute (like a `+2 sword` that increase
`strength` by `2`),
we need to have a clear definition of how the modifier is stored.

## Decision

Modifier format:

```json
{
    "type": "string",
    "value": "int",
    "prerequisites?": {
        "numberRequired": "int",
        "requirements": [
            {
                "type": "string",
                "condition": "string",
                "is": "bool"
            }
        ]
    }
}
```

Example:

```json
{
    "type": "self.attribute.strength.value",
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
```
