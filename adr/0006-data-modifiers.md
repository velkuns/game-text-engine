# Data - Modifiers

## Status

`draft`

## Context

When we have some magical items or effects that can modify the value of an ability (like a `+2 sword` that increase
`strength` by `2`),
we need to have a clear definition of how the modifier is stored.

## Decision

Modifier format:

```json
{
    "type": "string",
    "value": "int"
}
```

Example:

```json
{
    "type": "ability.strength",
    "value": 2
}
```
