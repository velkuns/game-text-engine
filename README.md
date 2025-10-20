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

### Loader (to load data from files / strings):

```php
<?php

declare(strict_types=1);

namespace Application;

use Velkuns\GameTextEngine\Api\Loader\JsonLoader;

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

use Velkuns\GameTextEngine\Api\Items;
use Velkuns\GameTextEngine\Api\Loader\JsonLoader;
use Velkuns\GameTextEngine\Element\Factory\ItemFactory;
use Velkuns\GameTextEngine\Element\Factory\ModifierFactory;

//~ Required factories
$itemFactory = new ItemFactory(new ModifierFactory());

//~ Loader
$loader    = new JsonLoader();
$itemsData = $loader->fromFile('/path/to/items.json');

//~ Items dictionary
$items = new Items($itemFactory);
$item->load($itemsData);

//~ Get an item by its name
$item = $items->get('Rusty Sword');
```

### Bestiary dictionary

```php
<?php

declare(strict_types=1);

namespace Application;

use Velkuns\GameTextEngine\Api\Bestiary;use Velkuns\GameTextEngine\Api\Items;
use Velkuns\GameTextEngine\Api\Loader\JsonLoader;
use Velkuns\GameTextEngine\Element\Condition\ConditionElementResolver;use Velkuns\GameTextEngine\Element\Condition\ConditionParser;use Velkuns\GameTextEngine\Element\Condition\ConditionValidator;use Velkuns\GameTextEngine\Element\Factory\AbilityFactory;
use Velkuns\GameTextEngine\Element\Factory\ConditionsFactory;use Velkuns\GameTextEngine\Element\Factory\EntityFactory;
use Velkuns\GameTextEngine\Element\Factory\ItemFactory;
use Velkuns\GameTextEngine\Element\Factory\ModifierFactory;
use Velkuns\GameTextEngine\Element\Factory\StatusFactory;

//~ Required factories
$modifierFactory  = new ModifierFactory();
$itemFactory      = new ItemFactory($modifierFactory);
$conditionFactory = new ConditionsFactory(new ConditionParser(), new ConditionElementResolver(), new ConditionValidator());
$entityFactory    = new EntityFactory(
    new AbilityFactory(), 
    new StatusFactory($modifierFactory, $conditionFactory), 
    $itemFactory
);

//~ Loader
$loader = new JsonLoader();

//~ Items dictionary (required to resolve item references in creatures inventory)
$items = new Items($itemFactory);
$item->load($loader->fromFile('/path/to/items.json'));

//~ Bestiary dictionary
$bestiary = new Bestiary($entityFactory, $items);
$bestiary->load($loader->fromFile('/path/to/bestiary.json'));

//~ Get a creature by its name
$entity = $bestiary->get('Goblin');
```


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
