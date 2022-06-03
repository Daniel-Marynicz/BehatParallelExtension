<?php

namespace DMarynicz\Tests\Event;

use DMarynicz\BehatParallelExtension\Event\ParallelTestsCompleted;
use PHPUnit\Framework\TestCase;

class ParallelTestCompletedTest extends TestCase
{
    public function testParallelTestCompleted(): void
    {
        new ParallelTestsCompleted();
        $this->assertTrue(true);
    }
}
