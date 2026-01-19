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
use DMarynicz\BehatParallelExtension\Util\ProcessFactory;
use DMarynicz\BehatParallelExtension\Util\SymfonyProcessFactory;
use Symfony\Component\Process\Process;

class TaskWorker implements Worker
{
    /** @var array<string, string> */
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

    /** @var ProcessFactory */
    private $processFactory;

    /**
     * @param array<string, string> $environment
     * @param int                   $workerId
     */
    public function __construct(
        Queue $queue,
        $environment,
        EventDispatcherDecorator $eventDispatcher,
        $workerId,
        ?ProcessFactory $processFactory = null
    ) {
        $this->environment     = $environment;
        $this->queue           = $queue;
        $this->eventDispatcher = $eventDispatcher;
        $this->workerId        = $workerId;
        $this->eventDispatcher->dispatch(new WorkerCreated($this), WorkerCreated::WORKER_CREATED);
        if (! $processFactory instanceof ProcessFactory) {
            $processFactory = new SymfonyProcessFactory();
        }

        $this->processFactory = $processFactory;
    }

    public function start(): void
    {
        if ($this->isStarted()) {
            throw new Runtime('Worker is already started');
        }

        $this->started = true;
        $this->next();
    }

    public function wait(): void
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

    public function isRunning(): bool
    {
        return $this->currentProcess && $this->currentProcess->isRunning();
    }

    public function isStarted(): bool
    {
        return $this->started;
    }

    public function stop(): void
    {
        $this->started = false;
        if (! ($this->currentProcess instanceof Process)) {
            return;
        }

        $this->currentProcess->stop();
    }

    /**
     * @return array<string, string>
     */
    public function getEnvironment(): array
    {
        return $this->environment;
    }

    /**
     * @param array<string, string> $env
     */
    public function setEnvironment(array $env): void
    {
        $this->environment = $env;
    }

    public function getWorkerId(): int
    {
        return $this->workerId;
    }

    public function __destruct()
    {
        $this->eventDispatcher->dispatch(new WorkerDestroyed($this), WorkerDestroyed::WORKER_DESTROYED);
    }

    private function clearCurrent(): void
    {
        $this->currentTask    = null;
        $this->currentProcess = null;
    }

    private function next(): void
    {
        $this->currentTask = $this->queue->shift();
        if (! $this->currentTask instanceof TaskEntity) {
            $this->stop();

            return;
        }

        $before = new BeforeTaskTested($this->currentTask);
        $this->eventDispatcher->dispatch($before, BeforeTaskTested::BEFORE);
        $this->currentProcess = $this->processFactory->createNewProcess(
            $this->currentTask->getCommand(),
            null,
            $this->environment,
            null,
            null
        );
        $this->currentProcess->start();
    }
}
