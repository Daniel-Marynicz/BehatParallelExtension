<?php

use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\Config\Suite;
use DMarynicz\BehatParallelExtension\Extension as ParallelExtension;
use DMarynicz\Tests\Behat\Context\ChunkTestContext;
use DMarynicz\Tests\Behat\Context\SimulateTestContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withExtension(new Extension(ParallelExtension::class))
        ->withSuite((new Suite('suite01'))
            ->addContext(
                ChunkTestContext::class,
                ['filesPath' => '%paths.base%']
            )
            ->addContext(SimulateTestContext::class)
            ->withPaths('%paths.base%/suite01')));
