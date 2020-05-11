<?php

namespace DMarynicz\Tests\Util;

use DMarynicz\BehatParallelExtension\Util\SymfonyProcessFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class SymfonyProcessFactoryTest extends TestCase
{
    /**
     * @param array<string>      $command
     * @param string|null        $cwd
     * @param array<string>|null $environment
     * @param null               $input
     * @param float|int|null     $timeout
     *
     * @dataProvider dataProvider
     */
    public function testCreateNewProcess($command, $cwd, $environment, $input, $timeout)
    {
        $process = (new SymfonyProcessFactory())->createNewProcess($command, $cwd, $environment, $input, $timeout);
        $this->assertEquals(new Process($command, $cwd, $environment, $input, $timeout), $process);
    }

    /**
     * @return mixed[][]
     */
    public function dataProvider()
    {
        return [
            [
                [],
                null,
                ['some-var' => 'asda'],
                null,
                null,

            ],
            [
                [],
                null,
                ['some-var' => 'asda'],
                null,
                30,

            ],
        ];
    }
}
