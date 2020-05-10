<?php

namespace DMarynicz\Tests\Event;

use DMarynicz\BehatParallelExtension\Event\ParallelTestCompleted;
use PHPUnit\Framework\TestCase;

class ParallelTestCompletedTest extends TestCase
{
    public function testParallelTestCompleted()
    {
        new ParallelTestCompleted();
        $this->assertTrue(true);
    }
}
