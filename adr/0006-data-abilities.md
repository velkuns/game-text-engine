# Data - Abilities

## Status

`draft`

## Context

We have two types of abilities: base & compound.

Base abilities are simple, with a value that can be changed (increased or decreased) during the game.
- type: base
- initial: the initial value of the ability (immutable)
- current: the current value of the ability
- max: the maximum value of the ability (generally >= initial)
- constraints: (immutable, limits min & max of the ability values)
  - min: min value for the ability (in absolute, generally 0 or 1)
  - max: max value for the ability (in absolute) 
- rule (optional): a mathematical expression that defines how to calculate the initial value from other abilities

Compound abilities are calculated from other abilities, and cannot be changed directly.
- type: compound
- rule: a mathematical expression that defines how to calculate the ability from other abilities

## Decision

### Base ability
So, the format will be:
Base ability format:
```json
{
    "type": "string",
    "initial": "int",
    "current": "int",
    "max": "int",
    "constraints": {
        "min": "int",
        "max": "int"
    },
    "rule?": "string"
}
```

Example (for a vitality ability):
```json
{
    "type": "base",
    "rule": "strength + endurance",
    "initial": 12,
    "current": 3,
    "max": 13,
    "constraints": {
        "min": 1,
        "max": 24
    }
}
```

### Compound ability
Compound ability format:
```json
{
    "type": "string",
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
