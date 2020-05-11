<?php

namespace DMarynicz\Tests\Worker;

use DMarynicz\BehatParallelExtension\Event\EventDispatcherDecorator;
use DMarynicz\BehatParallelExtension\Task\Queue;
use DMarynicz\BehatParallelExtension\Task\TaskEntity;
use DMarynicz\BehatParallelExtension\Util\ProcessFactory;
use DMarynicz\BehatParallelExtension\Worker\TaskWorker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class TaskWorkerTest extends TestCase
{
    public function testStart()
    {
        $queue = $this->createQueueMock();
        $task  = $this->createTaskEntityMock();
        $task
            ->method('getCommand')
            ->willReturn([]);
        $queue
            ->method('shift')
            ->willReturn($task);

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory
            ->method('createNewProcess')
            ->willReturn($this->createMock(Process::class));

        $worker = new TaskWorker(
            $queue,
            ['some' => 'env'],
            $this->createEventDispatcherDecoratorMock(),
            258,
            $processFactory
        );
        $worker->start();
        $worker->wait();
        $this->assertTrue($worker->isStarted());
        $worker->stop();
        $this->assertFalse($worker->isStarted());
        $this->assertEquals(258, $worker->getWorkerId());
        $this->assertEquals(['some' => 'env'], $worker->getEnvironment());
        $worker->setEnvironment(['some other' => 'env']);
        $this->assertEquals(['some other' => 'env'], $worker->getEnvironment());
    }

    /**
     * @return MockObject|TaskEntity
     */
    private function createTaskEntityMock()
    {
        return $this->createMock(TaskEntity::class);
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
