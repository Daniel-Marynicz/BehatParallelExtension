<?php

namespace DMarynicz\BehatParallelExtension\Event;

use Behat\Testwork\Event\Event;
use DMarynicz\BehatParallelExtension\Task\Task;
use Symfony\Component\Process\Process;

class AfterTaskTested extends Event
{
    const AFTER = 'parallel_extension.after_task_tested';

    /** @var Task */
    private $task;

    /** @var Process<string, string> */
    private $process;

    /**
     * @param Process<string, string> $process
     */
    public function __construct(Task $task, Process $process)
    {
        $this->task    = $task;
        $this->process = $process;
    }

    /**
     * @return Task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @return Process<string, string>
     */
    public function getProcess()
    {
        return $this->process;
    }
}
