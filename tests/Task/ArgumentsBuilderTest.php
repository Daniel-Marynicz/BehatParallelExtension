<?php

namespace DMarynicz\Tests\Task;

use DMarynicz\BehatParallelExtension\Task\ArgumentsBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Process\PhpExecutableFinder;

class ArgumentsBuilderTest extends TestCase
{
    protected function setUp()
    {
        if (defined('BEHAT_BIN_PATH')) {
            return;
        }

        define('BEHAT_BIN_PATH', 'behat');
    }

    /**
     * @param array<string,mixed> $options
     * @param array<string,mixed> $arguments
     * @param string              $phpPath
     * @param string              $path
     * @param string[]            $expected
     *
     * @throws ReflectionException
     *
     * @dataProvider buildArgumentsProvider
     */
    public function testBuildArguments($options, $arguments, $phpPath, $path, $expected)
    {
        $input  = $this->createInputInterfaceMock($options, $arguments);
        $finder = $this->createPhpExecutableFinder($phpPath);

        $builder = new ArgumentsBuilder($finder);
        $actual  = $builder->buildArguments($input, $path);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array<mixed>
     */
    public function buildArgumentsProvider()
    {
        return [
            [
                [
                    'some-true-option' => true,
                    'some-false-option' => false,
                    'parallel' => true,
                    'parallel-feature' => true,
                    'string-option' => 'string',
                    'integer-option' => 123,
                    'float-option' => 1.23,
                    'array-option' => [
                        'element1',
                        'element2',
                    ],
                ],
                ['paths' => 'path'],
                'php-binary',
                'path-some-test.feature:21',
                [
                    'php-binary',
                    'behat',
                    '--no-interaction',
                    '--fail-on-undefined-step',
                    '--some-true-option',
                    '--string-option',
                    'string',
                    '--integer-option',
                    123,
                    '--float-option',
                    1.23,
                    '--array-option',
                    'element1',
                    '--array-option',
                    'element2',
                    'path-some-test.feature:21',
                ],
            ],
            [
                [
                    'some-true-option' => true,
                    'some-false-option' => false,
                    'parallel' => true,
                    'parallel-feature' => true,
                    'string-option' => 'string',
                    'integer-option' => 123,
                    'float-option' => 1.23,
                    'array-option' => [
                        'element1',
                        'element2',
                    ],
                    'no-interaction' => true,
                    'fail-on-undefined-step' => true,
                ],
                ['paths' => 'path'],
                'php-binary',
                'path-some-test.feature:21',
                [
                    'php-binary',
                    'behat',
                    '--some-true-option',
                    '--string-option',
                    'string',
                    '--integer-option',
                    123,
                    '--float-option',
                    1.23,
                    '--array-option',
                    'element1',
                    '--array-option',
                    'element2',
                    '--no-interaction',
                    '--fail-on-undefined-step',
                    'path-some-test.feature:21',
                ],
            ],
        ];
    }

    /**
     * @param array<string,mixed> $options
     * @param array<string,mixed> $arguments
     *
     * @return MockObject|InputInterface
     */
    private function createInputInterfaceMock($options, $arguments)
    {
        $input = $this->createMock(InputInterface::class);
        $input
            ->method('getOptions')
            ->willReturn($options);
        $input
            ->method('getOption')
            ->willReturnCallback(static function ($name) use ($options) {
                return isset($options[$name]) ? $options[$name] : false;
            });
        $input
            ->method('getArguments')
            ->willReturn($arguments);

        return $input;
    }

    /**
     * @param string $phpBinPath
     *
     * @return MockObject|PhpExecutableFinder
     */
    private function createPhpExecutableFinder($phpBinPath = '/usr/bin/php7.4')
    {
        $finder = $this->createMock(PhpExecutableFinder::class);
        $finder->method('find')->willReturn($phpBinPath);

        return $finder;
    }
}
