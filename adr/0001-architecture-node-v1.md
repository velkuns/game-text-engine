# Architecture - Node - v1 - Mains nodes - Simple version

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

### v1 - Mains nodes - Simple version
```mermaid
---
Tables diagram: nodes (v1)
---
classDiagram
    
    class node_text {
        int node_text_id
        string node_text_content
    }
    
    class node_choice {
        int node_choice_id
        int node_text_id_from
        int node_text_id_to
        string node_choice_condition
        string node_choice_content
    }
```

For normal playbook game, the relationship between nodes will use node_text_id_from and node_action_id_to.

## Consequences
Pros:
- 

Cons:
-
