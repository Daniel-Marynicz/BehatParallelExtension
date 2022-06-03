<?php

namespace DMarynicz\Tests\Cli;

use DMarynicz\BehatParallelExtension\Cli\SigintController;
use DMarynicz\BehatParallelExtension\Event\EventDispatcherDecorator;
use DMarynicz\BehatParallelExtension\Event\ParallelTestsAborted;
use PHPUnit\Framework\MockObject\MockObject;

class SigintControllerTest extends ControllerTest
{
    /** @var SigintController */
    protected $controller;

    /** @var MockObject|EventDispatcherDecorator */
    protected $eventDispatcherDecorator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventDispatcherDecorator = $this->createMock(EventDispatcherDecorator::class);
        $this->controller               = new SigintController($this->eventDispatcherDecorator);
    }

    public function testConfigure(): void
    {
        $this->controller->configure($this->command);
        $this->assertTrue(true);
    }

    /**
     * @param int    $count
     * @param string $first
     * @param string $second
     * @param bool   $firstValue
     * @param bool   $secondValue
     *
     * @dataProvider executeDataProvider
     */
    public function testExecute($count, $first, $second, $firstValue, $secondValue): void
    {
        $this
            ->input
            ->expects($this->exactly($count))
            ->method('getOption')
            ->withConsecutive([$first], [$second])
            ->willReturnOnConsecutiveCalls($firstValue, $secondValue);

        $this->controller->execute($this->input, $this->output);
    }

    public function testAbortTests(): void
    {
        $this
            ->eventDispatcherDecorator
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                new ParallelTestsAborted(),
                ParallelTestsAborted::ABORTED
            );

        $this->controller->abortTests();
    }

    /**
     * @return mixed[][]
     */
    public function executeDataProvider(): array
    {
        return [
            [
                1,
                'parallel',
                'parallel-feature',
                true,
                false,
            ],
            [
                2,
                'parallel',
                'parallel-feature',
                false,
                true,
            ],
            [
                2,
                'parallel',
                'parallel-feature',
                false,
                false,
            ],
        ];
    }
}
