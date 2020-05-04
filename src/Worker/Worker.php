<?php

namespace DMarynicz\BehatParallelExtension\Worker;

use DMarynicz\BehatParallelExtension\Event\AfterTaskTested;
use DMarynicz\BehatParallelExtension\Event\BeforeTaskTested;
use DMarynicz\BehatParallelExtension\Event\WorkerCreated;
use DMarynicz\BehatParallelExtension\Event\WorkerDestroyed;
use DMarynicz\BehatParallelExtension\Exception\Runtime;
use DMarynicz\BehatParallelExtension\Task\Queue;
use DMarynicz\BehatParallelExtension\Task\Task;
use DMarynicz\BehatParallelExtension\Util\Assert;
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

    /** @var int */
    private $workerId;

    /**
     * @param string[] $env
     * @param int $workerId
     */
    public function __construct(Queue $queue, $env, EventDispatcherInterface $eventDispatcher, $workerId)
    {
        $this->env             = $env;
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

    private function next()
    {
        $this->currentTask = $this->queue->dequeue();
        if (! $this->currentTask instanceof Task) {
            $this->stop();

            return;
        }

        $before = new BeforeTaskTested($this->currentTask);
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
        if (! ($this->currentProcess instanceof Process)) {
            return;
        }

        $this->currentProcess->stop(0);
    }

    /**
     * @return string[]
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @param string[] $env
     * @return Worker
     */
    public function setEnv($env)
    {
        Assert::assertArray($env);
        $this->env = $env;
        return $this;
    }

    /**
     * @return int
     */
    public function getWorkerId()
    {
        return $this->workerId;
    }

    private function clearCurrent()
    {
        $this->currentTask    = null;
        $this->currentProcess = null;
    }

    public function __destruct()
    {
        $this->eventDispatcher->dispatch(new WorkerDestroyed($this), WorkerCreated::WORKER_CREATED);
    }
}
