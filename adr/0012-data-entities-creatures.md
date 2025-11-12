# Data - Non Player Characters & Creatures

## Status

`draft`

## Context

In any universe, players can interact with non players characters (NPC) or creatures.
So we need to define & store some NPC & creatures basic information.
For NPC (non creatures), it is globally similar to players data (see [Data - Players](./0005-data-players.md)), but without notes & background.


## Decision

### Monsters
For monsters:
Format:
```json
{
    "name": "string",
    "type": "creature",
    "coins": "int",
    "info": {
        "level": "int",
        "age": "int",
        "size": "tiny|small|medium|large|huge",
        "race": "string",
        "description": "string",
        "background": "",
        "notes": ""
    },
    "attributes": {
        "base": "list<BaseAttribute>",
        "compound": "list<CompoundAttribute>"
    },
    "traits": {
    "skills": "list<TraitInterface>",
    "states": "list<TraitInterface>",
    "blessings": "list<TraitInterface>",
    "curses": "list<TraitInterface>"
    },
    "inventory": "list<Item>"
}
```

Example:
```json
{
    "name": "Rat",
    "type": "creature",
    "coins": 0,
    "info": {
        "level": 1,
        "age": 0,
        "size": "tiny",
        "race": "rat",
        "description": "...",
        "background": "",
        "notes": ""
    },
    "attributes": {
        "base": "list<BaseAttribute>",
        "compound": "list<CompoundAttribute>"
    },
    "traits": {
        "skills": "list<TraitInterface>",
        "states": "list<TraitInterface>",
        "blessings": "list<TraitInterface>",
        "curses": "list<TraitInterface>"
    },
    "inventory": "list<Item>"
}
```
