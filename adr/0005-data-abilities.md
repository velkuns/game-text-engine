# Data - Attributes

## Status

`draft`

## Context

We have two types of attributes: base & compound.

Base attributes are simple, with a value that can be changed (increased or decreased) during the game.
- type: base
- initial: the initial value of the attribute (immutable)
- value: the current value of the attribute
- max: the maximum value of the attribute (generally >= initial)
- constraints: (immutable, limits min & max of the attribute values)
  - min: min value for the attribute (in absolute, generally 0 or 1)
  - max: max value for the attribute (in absolute) 
- rule (optional): a mathematical expression that defines how to calculate the initial value from other attributes

Compound attributes are calculated from other attributes, and cannot be changed directly.
- type: compound
- rule: a mathematical expression that defines how to calculate the attribute from other attributes

## Decision

### Base attribute
So, the format will be:
Base attribute format:
```json
{
    "type": "base",
    "name": "string",
    "value": "int",
    "max": "int",
    "constraints": {
        "min": "int",
        "max": "int"
    },
    "initial": "int",
    "rule?": "string"
}
```

Example (for a vitality attribute):
```json
{
    "type": "base",
    "name": "vitality",
    "rule": "strength + endurance",
    "initial": 12,
    "value": 3,
    "max": 13,
    "constraints": {
        "min": 1,
        "max": 24
    }
}
```

### Compound attribute
Compound attribute format:
```json
{
    "type": "compound",
    "name": "string",
    "rule": "string"
}
```

Example:
```json
{
    "type": "compound",
    "rule": "(strength + agility) / 2"
}
```

### Global Attributes format
```json
{
    "base": "list<BaseAttribute>",
    "compound": "list<CompoundAttribute>"
}
```
