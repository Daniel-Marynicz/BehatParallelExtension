<?php

namespace DMarynicz\BehatParallelExtension\Worker;

use DMarynicz\BehatParallelExtension\Exception\Runtime;
use DMarynicz\BehatParallelExtension\Queue\Queue;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class WorkerPoll
{
    const SERVICE_ID = 'parallel_extension.worker_poll';

    /** @var array<Worker> */
    private $workers = [];

    /** @var Queue */
    private $queue;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var int */
    private $maxWorkers = 2;

    /** @var bool */
    private $started = false;

    public function __construct(Queue $queue, EventDispatcherInterface $eventDispatcher)
    {
        $this->queue           = $queue;
        $this->eventDispatcher = $eventDispatcher;
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
        for ($i = 0; $i< $this->maxWorkers; $i++) {
            $worker = new Worker($this->queue, [], $this->eventDispatcher);
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
}
