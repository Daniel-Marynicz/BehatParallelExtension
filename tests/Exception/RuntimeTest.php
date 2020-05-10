<?php

namespace DMarynicz\Tests\Exception;

use DMarynicz\BehatParallelExtension\Exception\Runtime;
use PHPUnit\Framework\TestCase;

class RuntimeTest extends TestCase
{
    public function test()
    {
        $this->expectException(Runtime::class);

        throw new Runtime('some runtime exception');
    }
}
