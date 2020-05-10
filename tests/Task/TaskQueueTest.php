<?php

namespace DMarynicz\Tests\Task;

use DMarynicz\BehatParallelExtension\Task\TaskEntity;
use DMarynicz\BehatParallelExtension\Task\TaskQueue;
use PHPUnit\Framework\TestCase;

class TaskQueueTest extends TestCase
{
    public function testIsEmpty()
    {
        $queue = new TaskQueue();
        $this->assertTrue($queue->isEmpty());

        for ($expectedCount = 1; $expectedCount < 11; $expectedCount++) {
            $queue->dispatch($this->createMock(TaskEntity::class));
            $this->assertFalse($queue->isEmpty());
            $this->assertCount($expectedCount, $queue);
        }

        for ($expectedCount=9; $expectedCount>0; --$expectedCount) {
            $this->assertEquals($this->createMock(TaskEntity::class), $queue->shift());
            $this->assertFalse($queue->isEmpty());
            $this->assertCount($expectedCount, $queue);
        }

        $this->assertFalse($queue->isEmpty());
        $this->assertEquals($this->createMock(TaskEntity::class), $queue->shift());
        $this->assertTrue($queue->isEmpty());
        $this->assertCount(0, $queue);
    }
}
