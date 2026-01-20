<?php

namespace DMarynicz\Tests\Event;

use DMarynicz\BehatParallelExtension\Event\ParallelTestsAborted;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ParallelTestsAbortedTest extends TestCase
{
    public function test(): void
    {
        $event      = new ParallelTestsAborted();
        $reflection = new ReflectionClass($event);

        $this->assertTrue($reflection->hasConstant('ABORTED'));
        $this->assertSame('parallel_extension.parallel_tests_aborted', $reflection->getConstant('ABORTED'));
    }
}
