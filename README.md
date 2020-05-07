# Behat Parallel Extension

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://github.com/Daniel-Marynicz/BehatParallelExtension/blob/master/LICENSE)
[![Unix Status](https://travis-ci.com/Daniel-Marynicz/BehatParallelExtension.svg?branch=master)](https://travis-ci.com/Daniel-Marynicz/BehatParallelExtension)
[![Windows status](https://ci.appveyor.com/api/projects/status/i2y6sjmi6ae0xa7l/branch/master?svg=true)](https://ci.appveyor.com/project/Daniel-Marynicz/behat-parallel-extension/branch/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Daniel-Marynicz/BehatParallelExtension/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Daniel-Marynicz/BehatParallelExtension/?branch=master) 

### Introduction
This tool is for a speedup behat 3.x  tests by executing this tests in parallel mode.

### PHP compatibility
This tool requires php 5.6+ or higher.

The main reason for choosing php 5.6 is to be able to share this tool with more programmers :)

### Work

Currently, Work in progress, but the packagist package will soon be available.

### Screenshots

With parallel
![Alt text](with-parallel.png?raw=true "with parallel")


Without parallel
![Alt text](without-parallel.png?raw=true "without parallel")


### Installing Behat Parallel Extension

It will be by composer manager. TBD

### Configuration (Todo, it might still change)

Setting the environment variables can by be will done with your handler.


```yaml
default:
    extensions:
        DMarynicz\BehatParallelExtension\Extension:
          events:
            -
              eventName: parallel_extension.worker_created
              handler:
                - App\Tests\Behat\Event\WorkerCreatedHandler
                - handleWorkerCreated
```

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

### How to integrate this extension with symfony?

TBD  

### Usage

Use "--parallel" or "-l" parameter to specify number of concurrent workers. For example:

  ```
  $ vendor/behat -l 8
  Starting parallel scenario tests with 8 workers
   Feature: Parallel
    Scenario: Test behat tests with failed result
   3/3 [============================] 100% 12 secs/12 secs
  ```


