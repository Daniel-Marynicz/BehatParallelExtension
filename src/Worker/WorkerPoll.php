<?php

namespace DMarynicz\BehatParallelExtension\Worker;

use DMarynicz\BehatParallelExtension\Event\EventDispatcherDecorator;
use DMarynicz\BehatParallelExtension\Exception\Runtime;
use DMarynicz\BehatParallelExtension\Task\Queue;

class WorkerPoll implements Poll
{
    const SERVICE_ID = 'parallel_extension.worker_poll';

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
    public function setMaxWorkers($maxWorkers)
    {
        $this->maxWorkers = $maxWorkers;
    }

    /**
     * @return $this
     */
    public function start()
    {
        if ($this->isStarted()) {
            throw new Runtime('Worker Poll is already started');
        }

        $this->started = true;

        $this->setMaxWorkersToProperValue();

        for ($i = 0; $i< $this->maxWorkers; $i++) {
            $env    = isset($this->environments[$i]) ? $this->environments[$i] : [];
            $worker = new TaskWorker($this->queue, $env, $this->eventDispatcher, $i);
            $worker->start();
            $this->workers[] = $worker;
        }

        return $this;
    }

    public function wait()
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

    /**
     * @return bool
     */
    public function hasStartedWorkers()
    {
        foreach ($this->workers as $worker) {
            if ($worker->isStarted()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getTotalWorkers()
    {
        return count($this->workers);
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
        foreach ($this->workers as $worker) {
            $worker->stop();
        }
    }

    private function sleep()
    {
        usleep(1000);
    }

    private function setMaxWorkersToProperValue()
    {
        $envMaxWorkers = count($this->environments);
        if ($envMaxWorkers <= 0 || $this->maxWorkers <= $envMaxWorkers) {
            return;
        }

        $this->maxWorkers = $envMaxWorkers;
    }
}
