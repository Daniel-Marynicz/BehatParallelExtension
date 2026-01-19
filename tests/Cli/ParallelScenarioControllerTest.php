<?php

namespace DMarynicz\Tests\Cli;

use DMarynicz\BehatParallelExtension\Cli\ParallelScenarioController;
use DMarynicz\BehatParallelExtension\Task\TaskEntity;

class ParallelScenarioControllerTest extends ParallelControllerTest
{
    protected function setUp(): void
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

    public function testExecute(): void
    {
        $taskEntity = $this->createMock(TaskEntity::class);
        $this->taskFactory->method('createTasks')->willReturn([$taskEntity]);
        $this->input->method('getOption')->willReturnCallback(static function ($name) {
            if ($name === 'parallel') {
                return 32;
            }

            if ($name === 'parallel-chunk-size') {
                return 1;
            }

            return null;
        });
        $result = $this->controller->execute($this->input, $this->output);
        $this->assertEquals(0, $result);
    }
}
