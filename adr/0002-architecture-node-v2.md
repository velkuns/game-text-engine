# Architecture - Node - v2 - Mains nodes - With node_type & node_subtype - Rogue-like mode allowed

## Status
`draft`

## Context

### Playbook game
We need to "play" game books, which are text-based games where the player makes choices that affect the outcome of the story. 
The game is played through a web application interface and requires a backend to handle the game logic, state management, and user interactions.
Seems the better way to handle this sort of game is to use a graph structure to represent the game book, where each node is a page or a choice, 
and edges represent the choices leading to other pages.


## Decision
```mermaid
---
Graph: Graph of a game book
---

flowchart TD;
    ACT0(START) --> TXT0([TEXT 0]);
    TXT0 --> CHC1{CHOICE 1};
    TXT0 --> CHC2{CHOICE 2};
    TXT0 --> CHC3{CHOICE 3};
    CHC1 --> TXT1(TEXT 1);
    CHC2 --> TXT2(TEXT 2);
    CHC3 --> TXT3([TEXT 3]);
    TXT1 --> CHC4{CHOICE 4};
    TXT2 --> CHC5{CHOICE 5};
    CHC4 --> TXT4(...);
    CHC4 --> TXT5(...);
    CHC5 --> TXT6(...);

```

```mermaid
---
Tables diagram: Mains nodes (v2 - with node_type & node_subtype - rogue-like mode)
---
classDiagram
    class node_text {
        int node_text_id
        string node_text_content
        ----
        int node_type_id
        int node_subtype_id
    }
    
    class node_choice {
        int node_choice_id
        int node_text_id_from
        int node_text_id_to
        string node_choice_condition
        string node_choice_content
        ----
        int node_subtype_id_from
        int node_subtype_id_to
        int node_type_id_from
        int node_type_id_to
    }
```

```mermaid
---
Tables diagram: Type & Subtype
---
classDiagram
    class node_type{
        int node_type_id
        string node_type_name [house, path, place, ...]
    }

    class node_subtype{
        int node_subtype_id
        string node_subtype_name [bathroom, entrance, kitchen, ...]
    }
```

node_type_id(from|to) and node_subtype_id(from|to) will be used to connect nodes randomly by using those constraints.

## Consequences
Pros:
- 

Cons:
-
