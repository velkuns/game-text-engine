# Data - Items

## Status

`draft`

## Context

A virtual world generally have items that can be used, equipped, carried, bought, sold, stolen, etc.
So we need to define how an item is stored, with its properties.

## Decision

```json
{
    "name": "string",
    "type": "item",
    "subType?": "string",
    "description": "string",
    "modifiers": "list<Modifier>",
    "flags": "int",
    "equipped": "bool",
    "price": "int"
}
```

Example of subtypes:

- weapons:
    - sword
    - knife
    - axe
    - spear
    - bow
    - crossbow
    - staff
    - wand
    - dagger
    - mace
    - hammer
- potions:
    - healing
    - mana
    - strength
    - agility
    - endurance
    - vitality
    - intuition
- gear:
    - helmet
    - armor
    - boots
    - gloves
    - ring
    - amulet
    - belt
    - cloak
    - pants
    - shield
    - bracers
    - robe
    - tunic
- food:
    - bread
    - meat
    - fruit
    - vegetable
    - fish
    - cheese
    - soup
    - water
    - wine
    - ale
    - milk
- tools:
    - lockpick
    - rope
    - torch
    - lantern
    - shovel
    - fishing_rod

### Item flags

Flags as a combinations of bit values. There are useful to define some properties as boolean values.

-    `1` - `consumable`: can be consumed/used (removed after use)
-    `2` - `weapon`: can be used to attack
-    `4` - `shield`: can be used a shield to protect and block attack
-    `8` - `gear`: can be equipped in gear slot
-   `16` - `throwable`: knife, "splash potion", ...
-   `32` - `left_hand`: can be equipped in left hand
-   `64` - `right_hand`: can be equipped in right hand
-  `128` - `two_hands`: must be equipped in both hands
-  `256` - `magic`: item has magical properties
-  `512` - `quest`: item is a quest item (cannot be sold, dropped, etc...)
- `1024` - `melee`: item is a melee weapon
- `2048` - `ranged`: item is a ranged weapon
- `4096` - `ammunition`: item is ammunition (arrows, bolts, bullets, etc...)
- `8192` - `unique`: item is unique (only one can be owned, generally not tradable)

Examples:

- for a consumable throwable items, the flags value will be `1 + 16 = 17`.
- for a left hand weapon, the flags value will be `2 + 32 = 34`.
- for a right hand shield, the flags value will be `4 + 64 = 68`.
