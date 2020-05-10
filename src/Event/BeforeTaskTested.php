<?php

namespace DMarynicz\BehatParallelExtension\Event;

use Behat\Testwork\Event\Event;
use DMarynicz\BehatParallelExtension\Task\TaskEntity;

class BeforeTaskTested extends Event
{
    const BEFORE = 'parallel_extension.before_task_tested';

    /** @var TaskEntity */
    private $task;

    public function __construct(TaskEntity $task)
    {
        $this->task = $task;
    }

    /**
     * @return TaskEntity
     */
    public function getTask()
    {
        return $this->task;
    }
}
