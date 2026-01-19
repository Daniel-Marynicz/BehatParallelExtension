<?php

namespace DMarynicz\Tests\Event;

use DMarynicz\BehatParallelExtension\Event\ParallelTestsCompleted;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ParallelTestCompletedTest extends TestCase
{
    public function testParallelTestCompleted(): void
    {
        $event      = new ParallelTestsCompleted();
        $reflection = new ReflectionClass($event);

        $this->assertTrue($reflection->hasConstant('COMPLETED'));
        $this->assertSame('parallel_extension.parallel_tests_completed', $reflection->getConstant('COMPLETED'));
    }
}
