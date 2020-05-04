<?php


namespace DMarynicz\BehatParallelExtension\Event;

use Behat\Testwork\Event\Event;
use DMarynicz\BehatParallelExtension\Queue\Task;
use Symfony\Component\Process\Process;

class AfterTaskTested extends Event
{
    const AFTER = 'parallel_extension.after_task_tested';

    /**
     * @var Task
     */
    private $task;

    /**
     * @var Process
     */
    private $process;

    /**
     * @param Task $task
     * @param Process $process
     */
    public function __construct(Task $task, Process $process)
    {
        $this->task = $task;
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
     * @return Process
     */
    public function getProcess()
    {
        return $this->process;
    }
}
