<?php

namespace DMarynicz\Tests\Util;

use DMarynicz\BehatParallelExtension\Util\DetermineNumberOfProcessingUnits;
use PHPUnit\Framework\TestCase;

class DetermineNumberOfProcessingUnitsTest extends TestCase
{
    /** @var string */
    private $numberOfPressersEnvValue;

    public function testGetDetermineNumberOfProcessingUnits(): void
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
    public function testWindowsEnvMethod($expected): void
    {
        putenv('NUMBER_OF_PROCESSORS=' . $expected);
        if (stripos(PHP_OS, 'WIN') === 0) {
            //on windows we can't change this env variable
            $expected = getenv('NUMBER_OF_PROCESSORS');
        }

        $testedObject = new DetermineNumberOfProcessingUnits();
        $actual       = $testedObject->getNumberOfProcessingUnits();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return int[][]
     */
    public function envMethodProvider(): array
    {
        return [
            [1],
            [2],
            [4],
            [8],
        ];
    }

    protected function setUp(): void
    {
        $value = getenv('NUMBER_OF_PROCESSORS');
        if (! is_string($value)) {
            return;
        }

        $this->numberOfPressersEnvValue = $value;
    }

    protected function tearDown(): void
    {
        putenv('NUMBER_OF_PROCESSORS=' . $this->numberOfPressersEnvValue);
    }
}
