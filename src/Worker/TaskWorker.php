<?php

namespace DMarynicz\BehatParallelExtension\Worker;

use DMarynicz\BehatParallelExtension\Event\AfterTaskTested;
use DMarynicz\BehatParallelExtension\Event\BeforeTaskTested;
use DMarynicz\BehatParallelExtension\Event\EventDispatcherDecorator;
use DMarynicz\BehatParallelExtension\Event\WorkerCreated;
use DMarynicz\BehatParallelExtension\Event\WorkerDestroyed;
use DMarynicz\BehatParallelExtension\Exception\Runtime;
use DMarynicz\BehatParallelExtension\Task\Queue;
use DMarynicz\BehatParallelExtension\Task\TaskEntity;
use DMarynicz\BehatParallelExtension\Util\Assert;
use Symfony\Component\Process\Process;

class TaskWorker implements Worker
{
    /** @var string[] */
    private $environment;

    /** @var Queue */
    private $queue;

    /** @var EventDispatcherDecorator */
    private $eventDispatcher;

    /** @var bool */
    private $started = false;

    /** @var Process<string, string>|null */
    private $currentProcess;

    /** @var TaskEntity|null */
    private $currentTask;

    /** @var int */
    private $workerId;

    /**
     * @param string[] $environment
     * @param int      $workerId
     */
    public function __construct(Queue $queue, $environment, EventDispatcherDecorator $eventDispatcher, $workerId)
    {
        $this->environment     = $environment;
        $this->queue           = $queue;
        $this->eventDispatcher = $eventDispatcher;
        Assert::assertInt($workerId);
        $this->workerId = $workerId;
        $this->eventDispatcher->dispatch(new WorkerCreated($this), WorkerCreated::WORKER_CREATED);
    }

    public function start()
    {
        if ($this->isStarted()) {
            throw new Runtime('Worker is already started');
        }

        $this->started = true;
        $this->next();
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
        if (! ($this->currentProcess instanceof Process)) {
            return;
        }

        $this->currentProcess->stop(0);
    }

    /**
     * @return string[]
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param string[] $env
     *
     * @return Worker
     */
    public function setEnvironment($env)
    {
        Assert::assertArray($env);
        $this->environment = $env;

        return $this;
    }

    /**
     * @return int
     */
    public function getWorkerId()
    {
        return $this->workerId;
    }

    public function __destruct()
    {
        $this->eventDispatcher->dispatch(new WorkerDestroyed($this), WorkerCreated::WORKER_CREATED);
    }

    private function clearCurrent()
    {
        $this->currentTask    = null;
        $this->currentProcess = null;
    }

    private function next()
    {
        $this->currentTask = $this->queue->shift();
        if (! $this->currentTask instanceof TaskEntity) {
            $this->stop();

            return;
        }

        $before = new BeforeTaskTested($this->currentTask);
        $this->eventDispatcher->dispatch($before, BeforeTaskTested::BEFORE);
        $this->currentProcess = new Process(
            $this->currentTask->getCommand(),
            null,
            $this->environment,
            null,
            null
        );
        $this->currentProcess->setPty(true);
        $this->currentProcess->start();
    }
}
