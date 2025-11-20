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
- attributes (list of attributes, simple & compound)


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
    "attributes": "Attributes",
    "traits": "Traits",
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
    "attributes": {
        "simples": "list<SimpleAttribute>",
        "compounds": "list<CompoundAttribute>"
    },
    "traits": {
        "skills": "list<StatusInterface>",
        "states": "list<TraitInterface>",
        "blessings": "list<TraitInterface>",
        "curses": "list<TraitInterface>",
        "titles": "list<TraitInterface>"
    },
    "inventory": "list<Item>"
}
```
