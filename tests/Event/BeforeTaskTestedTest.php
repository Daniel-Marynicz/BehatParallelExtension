<?php

namespace DMarynicz\Tests\Event;

use DMarynicz\BehatParallelExtension\Event\BeforeTaskTested;
use DMarynicz\BehatParallelExtension\Task\TaskEntity;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class BeforeTaskTestedTest extends TestCase
{
    public function testBeforeTaskTestedTest()
    {
        $this->createMock(TaskEntity::class);
        $this->createMock(Process::class);
        $testedObject = new BeforeTaskTested(
            $this->createMock(TaskEntity::class)
        );
        $this->assertEquals($this->createMock(TaskEntity::class), $testedObject->getTask());
    }
}
