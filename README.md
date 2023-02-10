deable / console
================

Simple symfony console implementation for nette framework with synchronized commands.
Thanks to this library, you can simplify your workflow with the console commands and [Nette Framework](https://nette.org/).

Requirements
------------

This library was developed for PHP 7.3 or newer, designed for [Nette Framework](https://nette.org/) version 3.1 or newer.

Installation
------------

The best way to install this library is using [Composer](https://getcomposer.org/):

```sh
$ composer require deable/console
```

Usage
-----

Add extension to your application configuration: 

```yarn
extensions:
    console: Deable\Console\ConsoleExtension(%consoleMode%)

console:
    name: App Console
    url: https://my-project.lndo.site/
    locksDir: %tempDir%/console-locks

search:
    commands:
        in: %appDir%/Console
        files: *Command.php
```

To enable auto-detection of debug mode from console, use something like this in your `Bootstrap.php`:

```php
ConsoleHelper::setupMode($configurator, function () use ($configurator) {
    $configurator->setDebugMode(self::COOKIE_SECRET . '@' . ($_SERVER['REMOTE_ADDR'] ?? php_uname('n')));
});
```

To run your console application, create file `bin/console.php` like this:

```php
<?php

declare(strict_types=1);

use Symfony\Component\Console\Application;

require __DIR__ . '/../vendor/autoload.php';

exit(
App\Bootstrap::boot()
    ->createContainer()
    ->getByType(Application::class)
    ->run()
);

```

Contributing
------------
This is an open source, community-driven project. If you would like to contribute,
please follow the code format as used in current sources and submit a pull request.
