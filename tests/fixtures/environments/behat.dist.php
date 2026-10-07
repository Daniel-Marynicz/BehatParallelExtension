<?php

use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\Config\Suite;
use DMarynicz\BehatParallelExtension\Extension as ParallelExtension;
use DMarynicz\Tests\Behat\Context\EnvironmentContext;

return (new Config())
    ->withProfile((new Profile('default', [
        'translation' => ['locale' => 'en'],
    ]))
        ->withExtension(new Extension(ParallelExtension::class, [
            'environments' => [
                ['WORKER_ID' => 0],
                ['WORKER_ID' => 1],
                ['WORKER_ID' => 2],
                ['WORKER_ID' => 3],
            ],
        ]))
        ->withSuite((new Suite('suite01'))
            ->withContexts(EnvironmentContext::class)
            ->withPaths('%paths.base%/suite01')));
