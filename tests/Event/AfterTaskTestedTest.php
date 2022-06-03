<?php

namespace DMarynicz\Tests\Event;

use DMarynicz\BehatParallelExtension\Event\AfterTaskTested;
use DMarynicz\BehatParallelExtension\Task\TaskEntity;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class AfterTaskTestedTest extends TestCase
{
    public function testAfterTaskTested(): void
    {
        $testedObject = new AfterTaskTested(
            $this->createMock(TaskEntity::class),
            $this->createMock(Process::class)
        );
        $this->assertEquals($this->createMock(TaskEntity::class), $testedObject->getTask());
        $this->assertEquals($this->createMock(Process::class), $testedObject->getProcess());
    }
}
