<?php

namespace DMarynicz\Tests\Cli;

use DMarynicz\BehatParallelExtension\Cli\ParallelScenarioController;
use DMarynicz\BehatParallelExtension\Task\TaskEntity;

class ParallelScenarioControllerTest extends ParallelControllerTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->controller = new ParallelScenarioController(
            $this->decoratedController,
            $this->taskFactory,
            $this->poll,
            $this->queue,
            $this->eventDispatcherDecorator,
            $this->numberOfCores
        );
    }

    public function testExecute()
    {
        $taskEntity = $this->createMock(TaskEntity::class);
        $this->taskFactory->method('createTasks')->willReturn([$taskEntity]);
        $this->input->method('getOption')->with('parallel')->willReturn(32);
        $result = $this->controller->execute($this->input, $this->output);
        $this->assertEquals(0, $result);
    }
}
