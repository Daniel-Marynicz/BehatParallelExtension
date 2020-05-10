<?php

namespace DMarynicz\Tests\Exception;

use DMarynicz\BehatParallelExtension\Exception\Logic;
use PHPUnit\Framework\TestCase;

class LogicTest extends TestCase
{
    public function test()
    {
        $this->expectException(Logic::class);

        throw new Logic('some logic exception');
    }
}
