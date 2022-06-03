<?php

namespace DMarynicz\Tests\Cli;

use DMarynicz\BehatParallelExtension\Cli\ParallelFeatureController;
use DMarynicz\BehatParallelExtension\Task\TaskEntity;

class ParallelFeatureControllerTest extends ParallelControllerTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new ParallelFeatureController(
            $this->decoratedController,
            $this->taskFactory,
            $this->poll,
            $this->queue,
            $this->eventDispatcherDecorator,
            $this->numberOfCores
        );
    }

    public function testExecute(): void
    {
        $taskEntity = $this->createMock(TaskEntity::class);
        $this->taskFactory->method('createTasks')->willReturn([$taskEntity]);
        $this->input->method('getOption')->with('parallel-feature')->willReturn(32);
        $result = $this->controller->execute($this->input, $this->output);
        $this->assertEquals(0, $result);
    }
}
