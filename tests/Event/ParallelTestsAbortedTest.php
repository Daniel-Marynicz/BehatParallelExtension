<?php

namespace DMarynicz\Tests\Event;

use DMarynicz\BehatParallelExtension\Event\ParallelTestsAborted;
use PHPUnit\Framework\TestCase;

class ParallelTestsAbortedTest extends TestCase
{
    public function test(): void
    {
        new ParallelTestsAborted();
        $this->assertTrue(true);
    }
}
