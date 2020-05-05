<?php

namespace DMarynicz\BehatParallelExtension\Event;

use Behat\Testwork\Event\Event;

class ParallelTestCompleted extends Event
{
    const COMPLETED = 'parallel_extension.parallel_test_completed';
}
