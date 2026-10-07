<?php

use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\Config\Suite;
use DMarynicz\BehatParallelExtension\Extension as ParallelExtension;
use DMarynicz\Tests\Behat\Context\SimulateTestContext;

return (new Config())
    ->withProfile((new Profile('default', [
        'translation' => ['locale' => 'en'],
    ]))
        ->withExtension(new Extension(ParallelExtension::class))
        ->withSuite((new Suite('suite01'))
            ->withContexts(SimulateTestContext::class)
            ->withPaths('%paths.base%/suite01'))
        ->withSuite((new Suite('suite02'))
            ->withContexts(SimulateTestContext::class)
            ->withPaths('%paths.base%/suite02'))
        ->withSuite((new Suite('suite03'))
            ->withContexts(SimulateTestContext::class)
            ->withPaths('%paths.base%/suite03'))
        ->withSuite((new Suite('suite04'))
            ->withContexts(SimulateTestContext::class)
            ->withPaths('%paths.base%/suite04')));
