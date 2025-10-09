# component-template
[![Current version](https://img.shields.io/packagist/v/eureka/component-template.svg?logo=composer)](https://packagist.org/packages/eureka/component-template)
[![Supported PHP version](https://img.shields.io/static/v1?logo=php&label=PHP&message=7.4%20-%208.2&color=777bb4)](https://packagist.org/packages/eureka/component-template)
![CI](https://github.com/eureka-framework/component-template/workflows/CI/badge.svg)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=eureka-framework_component-template&metric=alert_status)](https://sonarcloud.io/dashboard?id=eureka-framework_component-template)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=eureka-framework_component-template&metric=coverage)](https://sonarcloud.io/dashboard?id=eureka-framework_component-template)

## Why?

Template for new components



## Installation

If you wish to install it in your project, require it via composer:

```bash
composer require eureka/component-template
```



## Usage

Usage:
```php
<?php

// Sample code here
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
You can run tests (with coverage) on your side with following command:
```bash
make tests
```

For prettier output (but without coverage), you can use the following command:
```bash
make testdox # run tests without coverage reports but with prettified output
```

#### Code Style
You also can run code style check with following commands:
```bash
make phpcs
```

You also can run code style fixes with following commands:
```bash
make phpcbf
```

#### Static Analysis
To perform a static analyze of your code (with phpstan, lvl 9 at default), you can use the following command:
```bash
make analyze
```

Minimal supported version:
```bash
make php74compatibility
```

Maximal supported version:
```bash
make php82compatibility
```

#### CI Simulation
And the last "helper" commands, you can run before commit and push, is:
```bash
make ci  
```


## License

This project is licensed under the MIT License - see the `LICENSE` file for details
