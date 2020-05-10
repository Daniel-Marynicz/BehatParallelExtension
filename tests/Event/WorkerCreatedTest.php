<?php

namespace DMarynicz\Tests\Event;

use DMarynicz\BehatParallelExtension\Event\WorkerCreated;
use DMarynicz\BehatParallelExtension\Worker\Worker;
use PHPUnit\Framework\TestCase;

class WorkerCreatedTest extends TestCase
{
    public function testWorkerCreatedTest()
    {
        $testedObject = new WorkerCreated($this->createMock(Worker::class));
        $this->assertEquals($this->createMock(Worker::class), $testedObject->getWorker());
    }
}
