<?php

namespace DMarynicz\BehatParallelExtension\Event;

use Behat\Testwork\Event\Event;
use DMarynicz\BehatParallelExtension\Queue\Task;

class BeforeTaskTested extends Event
{
    const BEFORE = 'parallel_extension.before_task_tested';

    /** @var Task */
    private $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * @return Task
     */
    public function getTask()
    {
        return $this->task;
    }
}
