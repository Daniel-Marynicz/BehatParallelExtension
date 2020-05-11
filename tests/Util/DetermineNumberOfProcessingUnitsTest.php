<?php

namespace DMarynicz\Tests\Util;

use DMarynicz\BehatParallelExtension\Util\DetermineNumberOfProcessingUnits;
use PHPUnit\Framework\TestCase;

class DetermineNumberOfProcessingUnitsTest extends TestCase
{
    /** @var string */
    private $numberOfPressersEnvValue;

    public function testGetDetermineNumberOfProcessingUnits()
    {
        $testedObject = new DetermineNumberOfProcessingUnits();
        $result       = $testedObject->getNumberOfProcessingUnits();
        $this->assertTrue($result > 0);
    }

    /**
     * @param int $expected
     *
     * @dataProvider  envMethodProvider
     * @depends testGetDetermineNumberOfProcessingUnits
     */
    public function testWindowsEnvMethod($expected)
    {
        putenv('NUMBER_OF_PROCESSORS=' . $expected);
        $testedObject = new DetermineNumberOfProcessingUnits();
        $actual       = $testedObject->getNumberOfProcessingUnits();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return int[][]
     */
    public function envMethodProvider()
    {
        return [
            [1],
            [2],
            [4],
            [8],
        ];
    }

    protected function setUp()
    {
        $value = getenv('NUMBER_OF_PROCESSORS');
        if (! is_string($value)) {
            return;
        }

        $this->numberOfPressersEnvValue = $value;
    }

    protected function tearDown()
    {
        putenv('NUMBER_OF_PROCESSORS=' . $this->numberOfPressersEnvValue);
    }
}
