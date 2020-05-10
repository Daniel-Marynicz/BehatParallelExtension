<?php

namespace DMarynicz\Tests\Event;

use DMarynicz\BehatParallelExtension\Event\WorkerDestroyed;
use DMarynicz\BehatParallelExtension\Worker\Worker;
use PHPUnit\Framework\TestCase;

class WorkerDestroyedTest extends TestCase
{
    public function testWorkerDestroyed()
    {
        $testedObject = new WorkerDestroyed($this->createMock(Worker::class));
        $this->assertEquals($this->createMock(Worker::class), $testedObject->getWorker());
    }
}
