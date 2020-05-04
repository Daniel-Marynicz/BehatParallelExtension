<?php


namespace DMarynicz\BehatParallelExtension\Worker;


use DMarynicz\BehatParallelExtension\Exception\RuntimeException;
use DMarynicz\BehatParallelExtension\Queue\Queue;
use DMarynicz\BehatParallelExtension\Queue\Result;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class WorkerPoll
{
    const SERVICE_ID = 'parallel_extension.worker_poll';

    /**
     * @var array<Worker>
     */
    private $workers = [];

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var int
     */
    private $maxWorkers=2;

    /**
     * @var bool
     */
    private $started = false;

    /**
     * @param Queue $queue
     * @param int $maxWorkers
     */
    public function __construct(Queue $queue, EventDispatcherInterface $eventDispatcher)
    {
        $this->queue = $queue;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return $this
     */
    public function start()
    {
        if ($this->isStarted()) {
            throw new RuntimeException('Worker Poll is already started');
        }

        $this->started  = true;
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
                return  true;
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