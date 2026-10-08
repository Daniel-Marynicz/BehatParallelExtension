<?php

use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\Config\Suite;
use DMarynicz\BehatParallelExtension\Extension as ParallelExtension;
use DMarynicz\Tests\Behat\Context\EnvironmentContext;
use DMarynicz\Tests\Behat\Context\ParallelBehatContext;
use DMarynicz\Tests\Behat\Context\SimulateTestContext;
use DMarynicz\Tests\Behat\Extension\TestsSkipperExtension;

return (new Config())
    ->withProfile((new Profile('default', [
        'gherkin' => [
            'filters' => ['tags' => '~@skip'],
        ],
        'translation' => ['locale' => 'en'],
    ]))
        ->withExtension(new Extension(ParallelExtension::class))
        ->withExtension(new Extension(TestsSkipperExtension::class))
        ->withSuite((new Suite('suite01'))
            ->withContexts(
                ParallelBehatContext::class,
                EnvironmentContext::class,
                SimulateTestContext::class
            )
            ->withPaths('%paths.base%/features')));
