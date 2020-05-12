<?php

namespace DMarynicz\BehatParallelExtension\Event;

use Behat\Testwork\Event\Event;

class ParallelTestsAborted extends Event
{
    const ABORTED = 'parallel_extension.parallel_tests_aborted';
}
