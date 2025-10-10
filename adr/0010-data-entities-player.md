# Data - Player

## Status

`draft`

## Context

Need to define & store some player basic information, like:
- name
- description
- background
- size (in cm)
- age (in years)
- notes
- inventory (list of items with quantity & notes)
- abilities (list of abilities, base & compound)


## Decision

So, the format will be:
Format:
```json
{
    "name": "string",
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
    "name": "player #1",
    "info": {
        "level": "1",
        "size": "175",
        "age": "20",
        "race": "human",
        "description": "...",
        "background": "...",
        "notes": "..."
    },
    "abilities": {
        "base": "list<BaseAbility>",
        "compound": "list<CompoundAbility>"
    },
    "statuses": {
        "skills": "list<Status>",
        "states": "list<Status>",
        "blessings": "list<Status>",
        "curses": "list<Status>",
        "titles": "list<Status>"
    },
    "inventory": "list<Item>"
}
```
