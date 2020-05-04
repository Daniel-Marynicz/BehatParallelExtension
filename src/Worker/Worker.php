<?php

namespace DMarynicz\BehatParallelExtension\Worker;

use DMarynicz\BehatParallelExtension\Event\AfterTaskTested;
use DMarynicz\BehatParallelExtension\Event\BeforeTaskTested;
use DMarynicz\BehatParallelExtension\Exception\Runtime;
use DMarynicz\BehatParallelExtension\Exception\UnexpectedValue;
use DMarynicz\BehatParallelExtension\Queue\Queue;
use DMarynicz\BehatParallelExtension\Queue\Task;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Process;

class Worker
{
    /** @var string[] */
    private $env;

    /** @var Queue */
    private $queue;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var bool */
    private $started = false;

    /** @var Process<string, string>|null */
    private $currentProcess;

    /** @var Task|null */
    private $currentTask;

    /**
     * @param string[] $env
     */
    public function __construct(Queue $queue, $env, EventDispatcherInterface $eventDispatcher)
    {
        $this->env             = $env;
        $this->queue           = $queue;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function start()
    {
        if ($this->isStarted()) {
            throw new Runtime('Worker is already started');
        }

        $this->started = true;
        $this->next();
    }

    private function next()
    {
        $this->currentTask = $this->queue->dequeue();
        if (!$this->currentTask instanceof Task) {
            throw new UnexpectedValue('Expected Task class');
        }
        $before            = new BeforeTaskTested($this->currentTask);
        $this->eventDispatcher->dispatch($before, BeforeTaskTested::BEFORE);
        $this->currentProcess = new Process(
            $this->currentTask->getCommand(),
            null,
            $this->env,
            null,
            120
        );
        $this->currentProcess->setPty(true);
        $this->currentProcess->start();
    }

    public function wait()
    {
        if ($this->isRunning()) {
            return;
        }

        if ($this->currentTask && $this->currentProcess) {
            $tested = new AfterTaskTested($this->currentTask, $this->currentProcess);
            $this->eventDispatcher->dispatch($tested, AfterTaskTested::AFTER);
            $this->clearCurrent();
        }

        if ($this->queue->isEmpty()) {
            $this->started = false;

            return;
        }

        $this->next();
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        return $this->currentProcess && $this->currentProcess->isRunning();
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        return $this->started;
    }

    public function stop()
    {
        $this->started = false;
        if ($this->currentProcess instanceof Process) {
            $this->currentProcess->stop(0);
        }
    }

    private function clearCurrent()
    {
        $this->currentTask    = null;
        $this->currentProcess = null;
    }
}
