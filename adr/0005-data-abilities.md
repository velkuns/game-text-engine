# Data - Attributes

## Status

`draft`

## Context

We have two types of attributes: simple & compound.

Simple attributes are simple, with a value that can be changed (increased or decreased) during the game.
- type: simple
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

### Simple attribute
So, the format will be:
Simple attribute format:
```json
{
    "type": "simple",
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
    "type": "simple",
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
    "simple": "list<SimpleAttribute>",
    "compound": "list<CompoundAttribute>"
}
```
