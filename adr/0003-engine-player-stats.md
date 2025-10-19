# Architecture - Entity statistics

## Status
`draft`

## Context
Dans les livres-jeux, les combats sont souvent basés sur des statistiques de joueur et d'adversaire. Cela permet de 
simuler des combats de manière plus réaliste et stratégique, en tenant compte des forces et faiblesses de chaque 
personnage.
Cela permet également de pouvoir créer différents types de personnages, avec des statistiques variées, et de rendre les
combats moins linéaires. Les statistiques peuvent aussi être utilisés pour d'autres mécaniques de jeu.

## Decision
### Entity stats game
Pour faire des combats un joueur doit avoir des statistiques de combat, comme la force, l'agilité, l'endurance, etc.
Après quelques réflexions, je suis arrivé à cet ensemble de statistiques de base pour un joueur dans un jeu de type "playbook".
- strength (force) : reflète la puissance physique du personnage
- endurance (Endurance) : reflète la capacité du personnage à résister à la fatigue et aux blessures
- agility (agilité) : reflète la rapidité et la coordination du personnage
- intuition (intuition) : reflète la capacité du personnage à percevoir son environnement et à réagir rapidement

Les statistiques vont de 0 à 100, où 0 est le minimum et 100 le maximum.
Un joueur neutre de base sera à 50 dans chaque statistique.

Puis pour le futur :
- skill (compétence) : reflète l'habileté du personnage dans des tâches spécifiques et sa maitrise des compétences
- magic (magie) : reflète la capacité du personnage à utiliser la magie ou des pouvoirs surnaturels

### Combats system
Pour les combats, l'idée est de se baser sur les statistiques du joueur et de l'adversaire pour déterminer les 
probabilités de succès et les dégâts infligés.

- _Attack_ -> **(strength + agility)** : Force + agilité pour représenter sa capacité à porter une offensive efficace
- _Defense_ -> **(endurance + intuition) * 2** : Endurance et intuition pour représenter sa capacité à résister aux attaques et à anticiper les mouvements de l'adversaire

Hit chance: Entity Attack / Enemy Defense

#### Examples

Example 1:

|          | strength | agility | endurance | intuition |
|----------|----------|---------|-----------|-----------|
| Entity   | 6        | 6       | 6         | 6         |
| Enemy    | 6        | 6       | 6         | 6         |

- Entity Attack = 6 + 6 = 100
- Enemy Defense = (6 + 6) * 2 = 200
- Hit chance = 12 / 24 = 0.5 (50%)

Donc en cas d'égalité des forces, le joueur aura 50% de chance de toucher l'adversaire, et inversement.


Example 2 :
Considérons que le joueur rencontre un rat enragé.
Ce rat est plus petit (donc moins de force et d'endurance), mais plus agile et intuitif.

|           | strength | agility | endurance | intuition |
|-----------|----------|---------|-----------|-----------|
| Entity    | 6        | 6       | 6         | 6         |
| Raged Rat | 2        | 10      | 2         | 10        |

- Entity Attack = 6 + 6 = 12
- Enemy Defense = (2 + 10) * 2 = 24
- Hit chance = 12 / 24 = 0.5 (50%)

On tombe encore sur 50%, dû à la nature du rat qui est plus agile et intuitif, mais moins fort et endurant.

Toutefois, en cas de réussite du joueur, comme ce dernier aura moins d'endurance, subira plus de dégâts.
De même, en cas de réussite du rat, il fera moins de dégâts au joueur, car moins de force.


### Life and damage
Pour la vie du joueur, on peut utiliser la formule suivante (statistique de base mais composée à l'initialisation) :
- vitality = endurance + strength

Pour les dégâts, on peut utiliser la formule suivante :
- damages = (character strength * 2) / enemy endurance + item damages

On appliquera des modificateurs en fonction de l'arme du joueur et de l'armure de l'adversaire.

Avec les exemples précédents, on peut calculer les dégâts infligés :
- Exemple 1 (sans modificateur) : (6 * 2) / 6 = 2 (Perte d'un quart de vie pour l'ennemi)
- Exemple 2 (sans modificateur) : (6 * 2) - 2 = 6 (Le rat meurt instantanément car il n'a que 4 de vitalité - ou hp)

### Future possibilities
On pourrait imaginer vouloir privilégier l'attaque sur une partie du corps adverse par exemple, pour cibler une faiblesse.
Dans ce cas, il faudrait ajuster la formule, peut-être en divisant la force d'attaque par 2 et l'endurance de défense par 2,
pour donner plus de poids à l'agilité et l'intuition, et permettre ainsi de doubler les dégâts infligés.
Cela pourrait permettre de quand même arriver à toucher un adversaire plus robuste, mais plus len

### Json format for storage & hydratation of objects
Pour le stockage, on peut stocker les statistiques sous format json, avec la représentation suivante :

Statistique principale:
```json
{
    "strength": {
        "type": "base",
        "initial": 6,
        "max": 6,
        "value": 6,
        "constraints": {
            "min": 0,
            "max": 12
        },
        "rule": null
    },
    "endurance": {
        "type": "base",
        "initial": 6,
        "max": 6,
        "value": 6,
        "constraints": {
            "min": 1,
            "max": 12
        },
        "rule": null
    },
    "agility": {
        "type": "base",
        "initial": 6,
        "max": 6,
        "value": 6,
        "constraints": {
            "min": 1,
            "max": 12
        },
        "rule": null
    },
    "intuition": {
        "type": "base",
        "initial": 6,
        "max": 6,
        "value": 6,
        "constraints": {
            "min": 1,
            "max": 12
        },
        "rule": null
    },
    "vitality": {
        "type": "base",
        "initial": 0, // 12 after initialization
        "max": 0,     // 12 after initialization
        "value": 0, // 12 after initialization
        "constraints": {
            "min": 0,
            "max": 24
        },
        "rule": "strength + endurance"
    },
    "attack": {
        "type": "compound",
        "rule": "strength + agility"
    },
    "defense": {
        "type": "compound",
        "rule": "endurance + intuition"
    }
}
```

Opérateur et ref supportés :
- `"{statistic name 1} [+-/*] {statistic name 2} ..."
- `(` and `)` could be used in future to support complex compound statistics

Exemple de statistique possible :
```json
{
    "critical_chance": {
        "type": "compound",
        "rule": "(agility + intuition) / 48"
    }
}
```
## Consequences
Pros:
- 

Cons:
-
