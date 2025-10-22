# Game Text Engine
[![Current version](https://img.shields.io/packagist/v/velkuns/game-text-engine.svg?logo=composer)](https://packagist.org/packages/velkuns/game-text-engine)
[![Supported PHP version](https://img.shields.io/static/v1?logo=php&label=PHP&message=8.3%20-%208.5&color=777bb4)](https://packagist.org/packages/velkuns/game-text-engine)
![CI](https://github.com/velkuns/game-text-engine/workflows/CI/badge.svg)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=velkuns_game-text-engine&metric=alert_status)](https://sonarcloud.io/dashboard?id=velkuns_game-text-engine)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=velkuns_game-text-engine&metric=coverage)](https://sonarcloud.io/dashboard?id=velkuns_game-text-engine)

## Why?

This is a PHP Game Text Engine to create games based on texts, with choices, items, and more.



## Installation

If you wish to install it in your project, require it via composer:

```bash
composer require velkuns/game-text-engine
```



## API Documentation

### Game Object

```php

declare(strict_types=1);

namespace Application;

use Velkuns\GameTextEngine\Api\Items;use Velkuns\GameTextEngine\Api\Player;use Velkuns\GameTextEngine\Util\Loader\JsonLoader;use Velunns\GameTextEngine\Api\GameApi;

//~ Factories
$modifierFactory  = new ModifierFactory();
$itemFactory      = new ItemFactory($modifierFactory);
$conditionFactory = new ConditionsFactory(new ConditionParser(), new ConditionElementResolver(), new ConditionValidator());
$graphFactory     = new GraphFactory($conditionFactory);
$entityFactory    = new EntityFactory(
    new AbilityFactory(), 
    new StatusFactory($modifierFactory, $conditionFactory), 
    $itemFactory
);

$items   = new Items($itemFactory);
$gameApi = new GameApi(
    new JsonLoader(),
    new Story($graphFactory),
    $items,
    new Bestiary($entityFactory, $items),
    new Player($entityFactory, $items),
    new Combat(new Randomizer(new Mt19937())),
);

//~ Load json data (can be from files or strings if came from database)
$storyData    = $game->loader->fromFile($dataDir . '/stories/test.json');
$itemsData    = $game->loader->fromFile($dataDir . '/items.json');
$bestiaryData = $game->loader->fromFile($dataDir . '/bestiary.json');
$playerData   = $game->loader->fromFile($dataDir . '/templates/player.json');

//~ Load data into the game api
$gameApi->load($storyData, $itemsData, $bestiaryData, $playerData);

/**
 * @param array{story: string, items: string, bestiary: string, player: string} $data Array of json data, to save in files or database
 */
$data = $gameApi->dump(/* true */); // true to pretty json output


```

### Loader (to load data from files / strings):

```php
<?php

declare(strict_types=1);

namespace Application;

use Velkuns\GameTextEngine\Util\Loader\JsonLoader;

$loader  = new JsonLoader();

//~ To load data from a file
$data = $loader->fromFile('/path/to/data.json');

//~ To load data from a string (JSON format, can be stored in database)
$data = $loader->fromString('{"key": "value"}');

```

### Items dictionary

```php
<?php

declare(strict_types=1);

namespace Application;

// [... game api init code here ... ]

//~ Get an item by its name
$item = $game->items->get('Rusty Sword');
```

### Bestiary dictionary

```php
<?php

declare(strict_types=1);

namespace Application;

// [... game api init code here ... ]

//~ Get a creature by its name
$entity = $gameApi->bestiary->get('Goblin');
```

### Story API

```php
<?php

declare(strict_types=1);

namespace Application;

// [... game api init code here ... ]

//~ Start the story - retrieve the first node of the story
$text = $gameApi->story->start();

// define $player before
  
//~ Get possible choices
$choices = $gameApi->story->getPossibleChoices($node->id, $player);

//~ Then display choices to the player, get his choice and advance the story
$playerChoice = $choices[0];
$nextText = $gameApi->story->goto($playerChoice->source, $playerChoice->target, $player/*[, $enemy]*/); // A validation is made to be sure the choice is valid
```

### Player API

```php
<?php

declare(strict_types=1);

namespace Application;

// [... game api init code here ... ]

//~ Create a new player based on the given data
$data = [
    'name'        => 'New Hero',
    'age'         => 25, // optional, default 20
    'race'        => 'elf', // optional, default 'human'
    'description' => 'A brave adventurer.', // optional, default ''
    'background'  => 'Born in a small village.', // optional, default ''
    'abilities'   => [,
        'strength'  => 10,
        'endurance' => 12,
        'agility'   => 14,
        'intuition' => 13,
    ], 
    'inventory' => ['Rusty Sword'], // optional, default []
];

$gameApi->player->new($data);

//~ Get player object
$player = $gameApi->player->player;


```

### Combat API

```php
<?php

declare(strict_types=1);

namespace Application;

// [... game api init code here ... ]

$enemies = [
    $gameApi->bestiary->get('Rat'), // get clone
    $gameApi->bestiary->get('Rat'), // get clone
];

$logs = $gameApi->combat->start($gameApi->player->player, $enemies);

//~ Display combat results
// ... your code to display combat turns ...

```

### Access to the Story's graph

```php
<?php

declare(strict_types=1);

namespace Application;

// [... game api init code here ... ]

$graph = $gameApi->story->graph;

//~ Manipulate graph nodes
$graph->addNode(new Node(...));
$graph->removeNode('node_id');

//~ Manipulate graph edges (between nodes).
$graph->addEdge(new Edge(...));
$graph->removeEdgeBetweenNodes('node_id_source', 'node_id_target');

```

> [!IMPORTANT]
> When add edges, source node and target node must have already been added to the graph.

## Contributing

See the [CONTRIBUTING](CONTRIBUTING.md) file.


### Install / update project

You can install project with the following command:
```bash
make install
```

And update with the following command:
```bash
make update
```

NB: For the components, the `composer.lock` file is not committed.

### Testing & CI (Continuous Integration)

#### Tests
You can run unit tests (with coverage) on your side with following command:
```bash
make php/tests
```

You can run integration tests (without coverage) on your side with following command:
```bash
make php/integration
```

For prettier output (but without coverage), you can use the following command:
```bash
make php/testdox # run tests without coverage reports but with prettified output
```

#### Code Style
You also can run code style check with following commands:
```bash
make php/cs-check
```

You also can run code style fixes with following commands:
```bash
make php/cs-fix
```

#### Check for missing explicit dependencies
You can check if any explicit dependency is missing with the following command:
```bash
make php/deps
```

#### Static Analysis
To perform a static analyze of your code (with phpstan, lvl 9 at default), you can use the following command:
```bash
make php/analyse
```

#### CI Simulation
And the last "helper" commands, you can run before commit and push, is:
```bash
make ci  
```

## License

This project is currently under The MIT License (MIT). See [LICENCE](LICENSE) file for more information.
