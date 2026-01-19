# Behat Parallel Extension

![Packagist Version](https://img.shields.io/packagist/v/dmarynicz/behat-parallel-extension?label=version)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://github.com/Daniel-Marynicz/BehatParallelExtension/blob/master/LICENSE)
[![PHP Tests](https://github.com/Daniel-Marynicz/BehatParallelExtension/actions/workflows/php.yml/badge.svg)](https://github.com/Daniel-Marynicz/BehatParallelExtension/actions/workflows/php.yml)

## Intro

This extension is for an executing behat 3.x tests in parallel mode.

![Behat Parallel Runner](parallel.apng?raw=true "Behat Parallel Extension with parallel mode enabled")

If you Properly integrate your app with this extension then it can be dramatically speed up your behat tests!

### Features

* Displays nice progress bar :).
* Extension can cancel your tests when you hit CTRL+C.
* When you have failed tests in **Parallel scenario** mode then can you rerun this test with Behat option `--rerun`.
* For each worker you can set environment variables.

### Main modes

Behat Parallel Extension can work in two main modes:

* **Parallel scenario** witch can be enabled by option `--parallel` or  `-l`.
* **Parallel feature** to enable this you need use behat option `--parallel-feature`.

 Parallel feature option does not support's `--rerun` option.

## Requirements

### PHP compatibility

This Behat extension requires php `5.6` or higher.
The main reason for choosing php 5.6 is to be able to share this tool with more programmers :).

## Installing Behat Parallel Extension

The most convenient way to install Behat Parallel Extension is by using [Composer]:

For more information about installing [Composer] please follow the documentation:
[https://getcomposer.org/download/](https://getcomposer.org/download/)

### Install

```
composer  require --dev dmarynicz/behat-parallel-extension
```

## Configuration

You can then activate and configure the extension in your `behat.yml` or `behat.yml.dist`.
In the array `environments` you can set you environment vars for each worker.
From this `environments` array depends on how much maximum you can run Workers.
If you do note set  `environments` array  then you can run an unlimited amount of Workers.
If the `environmental` array is defined, the maximum number of workers is the size of this array.

Example for maximum 4 Workers:

```yaml
default:
    # ...
    extensions:
        DMarynicz\BehatParallelExtension\Extension:
          environments:
            -
              ANY_YOUR_ENV_VARIABLE: with any value
              DATABASE_URL:        mysql://db_user:db_password@127.0.0.1:3306/db_name_00?serverVersion=5.7
            -
              ANY_YOUR_ENV_VARIABLE: with any value
              DATABASE_URL:        mysql://db_user:db_password@127.0.0.1:3306/db_name_01?serverVersion=5.7
            -
              ANY_YOUR_ENV_VARIABLE: with any value
              DATABASE_URL:        mysql://db_user:db_password@127.0.0.1:3306/db_name_02?serverVersion=5.7
            -
              ANY_YOUR_ENV_VARIABLE: with any value
              DATABASE_URL:        mysql://db_user:db_password@127.0.0.1:3306/db_name_03?serverVersion=5.7

```

Example for maximum an unlimited amount of Workers:

 ```yaml
 default:
     # ...
     extensions:
         DMarynicz\BehatParallelExtension\Extension: ~
 ```

## Integration with [SymfonyExtension] and [Symfony]

To integrate this extension with [SymfonyExtension] and [MinkExtension] you need install by [Composer] command packages

```sh
composer require --dev \
    friends-of-behat/mink \
    friends-of-behat/mink-extension \
    friends-of-behat/mink-browserkit-driver \
    friends-of-behat/symfony-extension
```

Configure the extension in your `behat.yml` or `behat.yml.dist` as in this example:
```yaml
default:
  suites:
    default:
      contexts:
        # Your contexts...
  extensions:
    Behat\MinkExtension:
      sessions:
        symfony:
          symfony: ~
    FriendsOfBehat\SymfonyExtension:
      # for symfony 5.3+ with symfony/runtime installed by composer command
      bootstrap: tests/bootstrap.php
      # for symfony versions older than 5.3
      #bootstrap: config/bootstrap.php

    DMarynicz\BehatParallelExtension\Extension:
      environments:
        - DATABASE_URL: "sqlite:///%%kernel.project_dir%%/var/data_test1.db"
          # doc for APP_CACHE_DIR https://symfony.com/doc/current/configuration/override_dir_structure.html#override-the-cache-directory
          APP_CACHE_DIR: "var/cache1"
          # SYMFONY_DOTENV_VARS does not have symfony's docs but without this tests will ignore env vars like DATABASE_URL, APP_CACHE_DIR and tests will not work
          SYMFONY_DOTENV_VARS:
        - DATABASE_URL: "sqlite:///%%kernel.project_dir%%/var/data_test2.db"
          APP_CACHE_DIR: "var/cache2"
          SYMFONY_DOTENV_VARS:
        - DATABASE_URL: "sqlite:///%%kernel.project_dir%%/var/data_test3.db"
          APP_CACHE_DIR: "var/cache3"
          SYMFONY_DOTENV_VARS:
        - DATABASE_URL: "sqlite:///%%kernel.project_dir%%/var/data_test4.db"
          APP_CACHE_DIR: "var/cache4"
          SYMFONY_DOTENV_VARS:

```

In `tests/bootstrap.php` for [Symfony] 5.3+ you need to have 

```php
<?php

use Symfony\Component\Dotenv\Dotenv;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';
(new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
```

Full example is available at [https://github.com/Daniel-Marynicz/BehatParallelExtension-IntegrationWithSymfony-Example](https://github.com/Daniel-Marynicz/BehatParallelExtension-IntegrationWithSymfony-Example)

## Usage

Use `--parallel` or `-l` option for start in parallel scenario mode.
  Or use `--parallel-feature` to start in parallel feature mode.
  Optionally you can to specify number of concurrent workers in these modes.
  Examples with enabled option `--colors`:

  ```
  $ vendor/bin/behat -l 8 --colors
  Starting parallel scenario tests with 8 workers
   Feature: Parallel
    Scenario: Test behat tests with failed result
   3/3 [============================] 100% 12 secs/12 secs
  ```

  ```
  $ vendor/bin/behat --parallel-feature 8 --colors
  Starting parallel scenario tests with 8 workers
   Feature: Parallel
    Scenario: Test behat tests with failed result
   3/3 [============================] 100% 12 secs/12 secs
  ```

## Test with the oldest supported PHP version

```bash
composer update --prefer-lowest --prefer-stable
docker run --rm -ti -v $(pwd):/app -w /app -u $(id -u):$(id -g) php:7.4-cli bash -c "vendor/bin/phpunit; vendor/bin/behat"
```

## Tools and Coding standards

The extension uses the following coding standards and quality tools:

### Doctrine Coding Standard

 The [Doctrine Coding Standard] with some exceptions for php 5.6 compatibility.
 The [Doctrine Coding Standard] is a set of rules for [PHP_CodeSniffer]. It is based on [PSR-1]
 and [PSR-12] , with some noticeable exceptions/differences/extensions.

 For more information about Doctrine Coding Standard please follow the documentation:

 [https://www.doctrine-project.org/projects/doctrine-coding-standard/en/latest/reference/index.html#introduction](https://www.doctrine-project.org/projects/doctrine-coding-standard/en/latest/reference/index.html#introduction)

### PHPStan

 [PHPStan] A php framework for auto testing your business expectations. PHPStan focuses on finding errors in your
 code without actually running it. It catches whole classes of bugs even before you write tests for the code.
  It moves PHP closer to compiled languages in the sense that the correctness of each line of the code can be checked
   before you run the actual line.

### PHPUnit

 [PHPUnit] The PHP Testing Framework with tests in directory [tests](tests)

### Behat

You can find [Behat] tests in directory [features](features)  and fixtures in [tests/fixtures](tests/fixtures) and some
classes for behat tests in directory [tests/Behat](tests/Behat).

[//]: #
   [PHP]: <https://www.php.net>
   [Symfony]: <http://symfony.com>
   [PHPUnit]: <https://phpunit.de>
   [Composer]: <https://getcomposer.org>
   [PHP_CodeSniffer]:  <https://github.com/squizlabs/PHP_CodeSniffer>
   [PHPStan]:   <https://github.com/phpstan/phpstan>
   [Doctrine Coding Standard]:   <https://www.doctrine-project.org/projects/doctrine-coding-standard/en/8.2/reference/index.html#introduction>
   [PSR-1]: <https://www.php-fig.org/psr/psr-1/>
   [PSR-12]: <https://www.php-fig.org/psr/psr-12/>
   [Behat]: <https://behat.org/>
   [SymfonyExtension]: <https://github.com/FriendsOfBehat/SymfonyExtension>
   [MinkExtension]: <https://github.com/FriendsOfBehat/MinkExtension>
