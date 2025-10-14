# Data - Non Player Characters & Creatures

## Status

`draft`

## Context

In any universe, players can interact with non players characters (NPC) or creatures.
So we need to define & store some NPC & creatures basic information.
For NPC (non creatures), it is globally similar to players data (see [Data - Players](./0005-data-players.md)), but without notes & background.


## Decision

### NPCs
So, the format will be:
Format:
```json
{
    "name": "string",
    "type": "npc",
    "coins": "int",
    "info": {
        "level": "int",
        "age": "int",
        "size": "string",
        "race": "string",
        "description": "string",
        "background": "string",
        "notes": "string"
    },
    "abilities": "Abilities",
    "statuses": "Statuses",
    "inventory": "Inventory"
}
```

Example:
```json
{
    "name": "NPC #1",
    "type": "npc",
    "info": {
        "level": 2,
        "age": 30,
        "size": "1m75",
        "race": "human",
        "description": "...",
        "background": "",
        "notes": ""
    },
    "abilities": {
        "base": "list<BaseAbility>",
        "compound": "list<CompoundAbility>"
    },
    "statuses": {
        "skills": "array<string, Status>",
        "states": "array<string, Status>",
        "blessings": "array<string, Status>",
        "curses": "array<string, Status>"
    },
    "inventory": "list<Item>"
}
```
