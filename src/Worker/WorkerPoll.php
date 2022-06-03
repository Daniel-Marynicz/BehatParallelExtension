<?php

namespace DMarynicz\BehatParallelExtension\Worker;

use DMarynicz\BehatParallelExtension\Event\EventDispatcherDecorator;
use DMarynicz\BehatParallelExtension\Exception\Runtime;
use DMarynicz\BehatParallelExtension\Task\Queue;

class WorkerPoll implements Poll
{
    /** @var array<Worker> */
    private $workers = [];

    /** @var Queue */
    private $queue;

    /** @var EventDispatcherDecorator */
    private $eventDispatcher;

    /** @var int */
    private $maxWorkers = 2;

    /** @var bool */
    private $started = false;

    /** @var array<array<mixed>> */
    private $environments;

    /**
     * @param array<array<mixed>> $environments
     */
    public function __construct(Queue $queue, EventDispatcherDecorator $eventDispatcher, $environments = [])
    {
        $this->queue           = $queue;
        $this->eventDispatcher = $eventDispatcher;
        $this->environments    = $environments;
    }

    /**
     * @param int $maxWorkers
     */
    public function setMaxWorkers($maxWorkers): void
    {
        $this->maxWorkers = $maxWorkers;
    }

    /**
     * @return $this
     */
    public function start(): Poll
    {
        if ($this->isStarted()) {
            throw new Runtime('Worker Poll is already started');
        }

        $this->started = true;

        $this->setMaxWorkersToProperValue();

        for ($i = 0; $i < $this->maxWorkers; $i++) {
            $env    = isset($this->environments[$i]) ? $this->environments[$i] : [];
            $worker = new TaskWorker($this->queue, $env, $this->eventDispatcher, $i);
            $worker->start();
            $this->workers[] = $worker;
        }

        return $this;
    }

    public function wait(): void
    {
        while ($this->hasStartedWorkers()) {
            foreach ($this->workers as $worker) {
                $worker->wait();
            }

            if (function_exists('pcntl_signal_dispatch')) {
                pcntl_signal_dispatch();
            }

            $this->sleep();
        }
    }

    public function hasStartedWorkers(): bool
    {
        foreach ($this->workers as $worker) {
            if ($worker->isStarted()) {
                return true;
            }
        }

        return false;
    }

    public function getTotalWorkers(): int
    {
        return count($this->workers);
    }

    public function isStarted(): bool
    {
        return $this->started;
    }

    public function stop(): void
    {
        foreach ($this->workers as $worker) {
            $worker->stop();
        }
    }

    private function sleep(): void
    {
        usleep(1000);
    }

    private function setMaxWorkersToProperValue(): void
    {
        $envMaxWorkers = count($this->environments);
        if ($envMaxWorkers <= 0 || $this->maxWorkers <= $envMaxWorkers) {
            return;
        }

        $this->maxWorkers = $envMaxWorkers;
    }
}
