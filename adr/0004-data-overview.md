# Data - Overview

## Status

`draft`

## Context

Text Game Engine need to manipulate data, like:
- player characteristics
- inventory
- places
- NPCs
- items
- monsters

But this game engine is agnostic of the data storage. So we need to define a way to "normalize" data structures,
that allow the system to "serialize" and "unserialize" the data, and build PHP objects based on those data.

## Decision

One way to achieve this is to `serialize` data in `json` format. The data serialized will be a valid json that contains
all required information to be parsed and converted into PHP objects directly from the Text Game Engine itself.

In that way, the application / game that will use the Text Game Engine will only need to store and retrieve data in
storage of it's' choice (database, files, etc...), and provide the data in json format to the Text Game Engine.

Also to avoid complexity in app/game side, the data will not be split into many part if they work together.
For example, a player will have all his characteristics in one json blob, instead of having one json blob for strength,
agility, etc... This will make it easier to manage data in the app/game side.

## Consequences

Pros:
- The game engine still agnostic of the data storage
- The game engine is responsible to `serialize` and `unserialize` data.
- All the logic of parsing or manipulating data is under the responsibility of the Text Game Engine

Cons:
- On the app/game side, each type of data become a `string` (serialized data), and don't have control on data directly
