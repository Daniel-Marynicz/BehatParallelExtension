<?php

namespace DMarynicz\BehatParallelExtension\Event;

use DMarynicz\BehatParallelExtension\Queue\Task;
use Behat\Testwork\Event\Event;

class BeforeTaskTested extends Event
{
    const BEFORE = 'parallel_extension.before_task_tested';

    private $task;

    /**
     * @param Task $task
     */
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
