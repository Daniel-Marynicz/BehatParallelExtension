<?php

namespace DMarynicz\Tests\Exception;

use DMarynicz\BehatParallelExtension\Exception\UnexpectedValue;
use PHPUnit\Framework\TestCase;

class UnexpectedValueTest extends TestCase
{
    public function test()
    {
        $this->expectException(UnexpectedValue::class);

        throw new UnexpectedValue('some UnexpectedValue exception');
    }
}
