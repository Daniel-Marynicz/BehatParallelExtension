<?php

namespace DMarynicz\Tests\Cli;

use Behat\Behat\Snippet\SnippetRepository;
use DMarynicz\BehatParallelExtension\Cli\SnippetsController;
use PHPUnit\Framework\MockObject\MockObject;

class SnippetsControllerTest extends ControllerTest
{
    /** @var MockObject|SnippetRepository */
    private $snippetRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->snippetRepository = $this->createMock(SnippetRepository::class);
        $this->controller        = new SnippetsController($this->decoratedController, $this->snippetRepository);
    }

    public function testConfigure()
    {
        $this->decoratedController->expects($this->once())->method('configure')->with($this->command);
        $this->controller->configure($this->command);
    }

    /**
     * @param int      $decoratedControllerResult
     * @param bool     $failOnUndefinedStep
     * @param string[] $undefinedSteps
     * @param string[] $snippets
     * @param int      $expectedResult
     *
     * @dataProvider executeProvider
     */
    public function testExecute(
        $decoratedControllerResult,
        $failOnUndefinedStep,
        $undefinedSteps,
        $snippets,
        $expectedResult
    ) {
        $this
            ->decoratedController
            ->expects($this->once())
            ->method('execute')
            ->with($this->input, $this->output)
            ->willReturn($decoratedControllerResult);

        $this->input->method('getOption')->willReturn($failOnUndefinedStep);
        $this->snippetRepository->method('getSnippets')->willReturn($snippets);
        $this->snippetRepository->method('getUndefinedSteps')->willReturn($undefinedSteps);

        $result = $this->controller->execute($this->input, $this->output);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array<mixed>
     */
    public function executeProvider()
    {
        return [
            [
                1,
                false,
                [],
                [],
                1,
            ],
            [
                0,
                false,
                [],
                [],
                0,
            ],
            [
                0,
                true,
                ['2dsa'],
                [],
                1,
            ],
            [
                0,
                true,
                [],
                ['sada'],
                1,
            ],
            [
                0,
                true,
                ['df'],
                ['sada'],
                1,
            ],

        ];
    }
}
