<?php

namespace DMarynicz\BehatParallelExtension\Event;

use Behat\Testwork\Event\Event;

class ParallelTestsCompleted extends Event
{
    const COMPLETED = 'parallel_extension.parallel_tests_completed';
}
