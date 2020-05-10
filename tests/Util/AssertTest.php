<?php

namespace DMarynicz\Tests\Util;

use DMarynicz\BehatParallelExtension\Exception\UnexpectedValue;
use DMarynicz\BehatParallelExtension\Util\Assert;
use PHPUnit\Framework\TestCase;

class AssertTest extends TestCase
{
    /**
     * @param mixed $value
     *
     * @dataProvider nonArrayProvider
     */
    public function testExceptionAssertArray($value)
    {
        $this->expectException(UnexpectedValue::class);
        $this->expectExceptionMessage('Expected array');
        Assert::assertArray($value);
    }

    public function testAssertArray()
    {
        Assert::assertArray([]);
        Assert::assertArray([1, 2, '22']);
        Assert::assertArray(['qweqwe' => 123213, 2, '22']);
        $this->assertTrue(true);
    }

    /**
     * @param mixed $value
     *
     * @dataProvider nonIntProvider
     */
    public function testExceptionAssertInt($value)
    {
        $this->expectException(UnexpectedValue::class);
        $this->expectExceptionMessage('Expected int');
        Assert::assertInt($value);
    }

    public function testAssertInt()
    {
        Assert::assertInt(-12);
        Assert::assertInt(-1);
        Assert::assertInt(0);
        Assert::assertInt(21);
        $this->assertTrue(true);
    }

    /**
     * @return array<mixed>
     */
    public function nonArrayProvider()
    {
        return [
            [ 1 ],
            [12.12],
            ['1'],
            [null],
            [true],
            [false],
            [null],
            [(object) 'ciao'],
        ];
    }

    /**
     * @return array<mixed>
     */
    public function nonIntProvider()
    {
        return [
            [ [2343,21321,'213213'] ],
            [ ['dad' => 312312] ],
            [12.12],
            ['1'],
            [null],
            [true],
            [false],
            [null],
            [(object) 'ciao'],
        ];
    }
}
