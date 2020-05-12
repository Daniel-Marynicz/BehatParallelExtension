# Behat Parallel Extension

![Packagist Version](https://img.shields.io/packagist/v/dmarynicz/behat-parallel-extension?label=version)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://github.com/Daniel-Marynicz/BehatParallelExtension/blob/master/LICENSE)
[![Unix Status](https://img.shields.io/travis/com/Daniel-Marynicz/BehatParallelExtension)](https://travis-ci.com/Daniel-Marynicz/BehatParallelExtension)
[![Windows status](https://ci.appveyor.com/api/projects/status/i2y6sjmi6ae0xa7l/branch/master?svg=true)](https://ci.appveyor.com/project/Daniel-Marynicz/behat-parallel-extension/branch/master)
[![Scrutinizer Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Daniel-Marynicz/BehatParallelExtension)](https://scrutinizer-ci.com/g/Daniel-Marynicz/BehatParallelExtension/?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/quality/g/Daniel-Marynicz/BehatParallelExtension)](https://scrutinizer-ci.com/g/Daniel-Marynicz/BehatParallelExtension/?branch=master)
[![Maintainability](https://api.codeclimate.com/v1/badges/82e68e2002a9ab8840ef/maintainability)](https://codeclimate.com/github/Daniel-Marynicz/BehatParallelExtension/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/82e68e2002a9ab8840ef/test_coverage)](https://codeclimate.com/github/Daniel-Marynicz/BehatParallelExtension/test_coverage)

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

## Tools and Coding standards

The extension uses the following coding standards and quality tools:

### Doctrine Coding Standard

 The [Doctrine Coding Standard] with some exceptions for php 5.6 compatibility.
 The [Doctrine Coding Standard] is a set of rules for [PHP_CodeSniffer]. It is based on [PSR-1]
 and [PSR-2] , with some noticeable exceptions/differences/extensions.

 For more information about Doctrine Coding Standard please follow the documentation:

 [https://www.doctrine-project.org/projects/doctrine-coding-standard/en/latest/reference/index.html#introduction](https://www.doctrine-project.org/projects/doctrine-coding-standard/en/latest/reference/index.html#introduction)

### PHPStan at level max

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
   [Doctrine Coding Standard]:   <https://www.doctrine-project.org/projects/doctrine-coding-standard/en/6.0/reference/index.html#introduction>
   [PSR-2]: <https://www.php-fig.org/psr/psr-2/>
   [PSR-1]: <https://www.php-fig.org/psr/psr-1/>
   [Behat]: <https://behat.org/>
 