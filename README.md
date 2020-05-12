Behat Parallel Extension
======

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://github.com/Daniel-Marynicz/BehatParallelExtension/blob/master/LICENSE)
[![Unix Status](https://img.shields.io/travis/com/Daniel-Marynicz/BehatParallelExtension)](https://travis-ci.com/Daniel-Marynicz/BehatParallelExtension)
[![Windows status](https://ci.appveyor.com/api/projects/status/i2y6sjmi6ae0xa7l/branch/master?svg=true)](https://ci.appveyor.com/project/Daniel-Marynicz/behat-parallel-extension/branch/master)
[![Scrutinizer Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Daniel-Marynicz/BehatParallelExtension)](https://scrutinizer-ci.com/g/Daniel-Marynicz/BehatParallelExtension/?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/quality/g/Daniel-Marynicz/BehatParallelExtension)](https://scrutinizer-ci.com/g/Daniel-Marynicz/BehatParallelExtension/?branch=master)
[![Maintainability](https://api.codeclimate.com/v1/badges/82e68e2002a9ab8840ef/maintainability)](https://codeclimate.com/github/Daniel-Marynicz/BehatParallelExtension/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/82e68e2002a9ab8840ef/test_coverage)](https://codeclimate.com/github/Daniel-Marynicz/BehatParallelExtension/test_coverage)

## Intro

This extension is for an executing behat 3.x tests in parallel mode.

![Alt text](parallel.apng?raw=true "with parallel")


 speedup behat 3.x tests by executing this tests in parallel mode.



### Features

Extension can execute tests in parallel mode. Also, supports behat standard option `--rerun` for rerunning failed tests. 

## Tools and Coding standards

The extension uses the following coding standards and quality tools:

#### Doctrine Coding Standard

 The [Doctrine Coding Standard] with some exceptions for php 5.6 compatibility.
 The [Doctrine Coding Standard] is a set of rules for [PHP_CodeSniffer]. It is based on [PSR-1]
 and [PSR-2] , with some noticeable exceptions/differences/extensions.
 
 For more information about Doctrine Coding Standard please follow the documentation: 
 
 https://www.doctrine-project.org/projects/doctrine-coding-standard/en/latest/reference/index.html#introduction

#### PHPStan at level max

 [PHPStan] A php framework for autotesting your business expectations. PHPStan focuses on finding errors in your code without actually running it. It catches whole classes of bugs even
  before you write tests for the code. It moves PHP closer to compiled languages in the sense that the correctness of
   each line of the code can be checked before you run the actual line.
#### PHPUnit

 [PHPUnit] The PHP Testing Framework with tests in directory [tests](tests)
#### Behat

You can find [Behat] tests in directory [features](features)  and fixtures in [tests/fixtures](tests/fixtures) and some 
classes for behat tests in directory [tests/Behat](tests/Behat).

## Requirements

### PHP compatibility
This tool requires php `5.6` or higher.
The main reason for choosing php 5.6 is to be able to share this tool with more programmers :)
And some symfony framework components in versions `^2.7.52 || ^3.0 || ^4.0 || ^5.0.8` more info about requremnts 
you can find in the [composer.json](composer.json).

## Screenshots

With parallel
![Alt text](with-parallel.png?raw=true "with parallel")


Without parallel
![Alt text](without-parallel.png?raw=true "without parallel")


## Installing Behat Parallel Extension

The most convenient way to install Behat Parallel Extension is by using Composer:
```
composer  require --dev dmarynicz/behat-parallel-extension
```


### 

Setting the environment variables can by be will done with your handler.
Or by directly by configuring workers:

```yaml
default:
    extensions:
        DMarynicz\BehatParallelExtension\Extension:
          environments:
            -
              ANY_YOUR_ENV_VARIABLE: with any value
              CACHE_DIR:           00-test
              SYMFONY_SERVER_PORT: 8000
              SYMFONY_SERVER_PID_FILE: .web-server-8000-pid
              DATABASE_URL:        mysql://db_user:db_password@127.0.0.1:3306/db_name_00?serverVersion=5.7
              SYMFONY_DOTENV_VARS:
            -
              ANY_YOUR_ENV_VARIABLE: with any value
              CACHE_DIR:           01-test
              SYMFONY_SERVER_PORT: 8001
              SYMFONY_SERVER_PID_FILE: .web-server-8001-pid
              DATABASE_URL:        mysql://db_user:db_password@127.0.0.1:3306/db_name_01?serverVersion=5.7
              SYMFONY_DOTENV_VARS:
            -
              ANY_YOUR_ENV_VARIABLE: with any value
              CACHE_DIR:           02-test
              SYMFONY_SERVER_PORT: 8002
              SYMFONY_SERVER_PID_FILE: .web-server-8002-pid
              DATABASE_URL:        mysql://db_user:db_password@127.0.0.1:3306/db_name_02?serverVersion=5.7
              SYMFONY_DOTENV_VARS:
            -
              ANY_YOUR_ENV_VARIABLE: with any value
              CACHE_DIR:           03-test
              SYMFONY_SERVER_PORT: 8003
              SYMFONY_SERVER_PID_FILE: .web-server-8003-pid
              DATABASE_URL:        mysql://db_user:db_password@127.0.0.1:3306/db_name_03?serverVersion=5.7
              SYMFONY_DOTENV_VARS:

```

In first method you can configure unlimited workers with your php code.
In second method if you want 16 workers then you must paste 16 elements in the array.

## How to integrate this extension with symfony?

TBD  

## Usage

Use "--parallel" or "-l" parameter to specify number of concurrent workers. For example:

  ```
  $ vendor/behat -l 8
  Starting parallel scenario tests with 8 workers
   Feature: Parallel
    Scenario: Test behat tests with failed result
   3/3 [============================] 100% 12 secs/12 secs
  ```


[//]: # 
   [PHP]: <https://www.php.net>
   [Symfony]: <http://symfony.com>
   [Docker]: <https://www.docker.com/>
   [Docker Compose]: <https://www.docker.com/>
   [PHPUnit]: <https://phpunit.de>
   [Composer]: <https://getcomposer.org>
   [PHP_CodeSniffer]:  <https://github.com/squizlabs/PHP_CodeSniffer>
   [PHPStan]:   <https://github.com/phpstan/phpstan>
   [Doctrine Coding Standard]:   <https://www.doctrine-project.org/projects/doctrine-coding-standard/en/6.0/reference/index.html#introduction>
   [PSR-2]: <https://www.php-fig.org/psr/psr-2/>
   [PSR-1]: <https://www.php-fig.org/psr/psr-1/>
   [PSR-12]: <https://www.php-fig.org/psr/psr-12/>
   [Behat]: <https://behat.org/>
   [Deptrac]: <https://github.com/sensiolabs-de/deptrac>
   [KnpPaginatorBundle]: <https://github.com/KnpLabs/KnpPaginatorBundle>
   [Behatch contexts]: https://github.com/Behatch/contexts 