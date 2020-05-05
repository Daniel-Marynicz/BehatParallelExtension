<?php

namespace DMarynicz\BehatParallelExtension\Event;

use Behat\Testwork\Event\Event;
use DMarynicz\BehatParallelExtension\Util\Assert;
use DMarynicz\BehatParallelExtension\Worker\Worker as Workman;

abstract class Worker extends Event
{
    /**
     * @var Workman
     */
    private $worker;

    /**
     * @param Workman $worker
     */
    public function __construct(Workman $worker)
    {
        $this->worker = $worker;
    }

    /**
     * @return Workman
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * @return int
     */
    public function getWorkerId()
    {
        return $this->workerId;
    }

}