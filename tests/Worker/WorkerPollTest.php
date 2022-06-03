<?php

namespace DMarynicz\Tests\Worker;

use DMarynicz\BehatParallelExtension\Event\EventDispatcherDecorator;
use DMarynicz\BehatParallelExtension\Exception\Runtime;
use DMarynicz\BehatParallelExtension\Task\Queue;
use DMarynicz\BehatParallelExtension\Worker\WorkerPoll;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WorkerPollTest extends TestCase
{
    /**
     * @param int $maxWorkers
     *
     * @dataProvider maxWorkersProvider
     */
    public function testWorkerPoll($maxWorkers): void
    {
        $poll = $this->getNewWorkerPoll();
        $poll->setMaxWorkers($maxWorkers);
        $poll->start();
        $poll->wait();
        $this->assertTrue($poll->isStarted());
        $this->assertEquals($maxWorkers, $poll->getTotalWorkers());
        $poll->stop();
    }

    public function testWorkerPoll2(): void
    {
        $poll = $this->getNewWorkerPoll();
        $poll->setMaxWorkers(3);
        $poll->start();
        $this->expectException(Runtime::class);
        $this->expectExceptionMessage('Worker Poll is already started');
        $poll->start();
    }

    /**
     * @return int[][]
     */
    public function maxWorkersProvider(): array
    {
        return [
            [1],
            [2],
            [21],
            [21],
        ];
    }

    private function getNewWorkerPoll(): WorkerPoll
    {
        $queue           = $this->createQueueMock();
        $eventDispatcher = $this->createEventDispatcherDecoratorMock();

        return new WorkerPoll($queue, $eventDispatcher, []);
    }

    /**
     * @return MockObject|Queue
     */
    private function createQueueMock()
    {
        return $this->createMock(Queue::class);
    }

    /**
     * @return MockObject|EventDispatcherDecorator
     */
    private function createEventDispatcherDecoratorMock()
    {
        return $this->createMock(EventDispatcherDecorator::class);
    }
}
