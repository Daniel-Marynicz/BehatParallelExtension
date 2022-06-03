<?php

namespace DMarynicz\Tests\Cli;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Testwork\Suite\Suite;
use DMarynicz\BehatParallelExtension\Cli\RerunController;
use DMarynicz\BehatParallelExtension\Event\AfterTaskTested;
use DMarynicz\BehatParallelExtension\Event\EventDispatcherDecorator;
use DMarynicz\BehatParallelExtension\Task\TaskEntity;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Process\Process;

class RerunControllerTest extends ControllerTest
{
    /** @var MockObject|EventDispatcherDecorator */
    private $eventsDispatcher;

    /** @var RerunController */
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventsDispatcher = $this->createMock(EventDispatcherDecorator::class);

        $this->controller = new RerunController($this->decoratedController, $this->eventsDispatcher);
    }

    public function testConfigure(): void
    {
        $this->decoratedController->expects($this->once())->method('configure')->with($this->command);
        $this->controller->configure($this->command);
    }

    public function testExecute(): void
    {
        $this
            ->decoratedController
            ->expects($this->once())
            ->method('execute')
            ->with($this->input, $this->output);

        $this->controller->execute($this->input, $this->output);
    }

    public function testCollectFailedTask(): void
    {
        $afterTested = $this->createMock(AfterTaskTested::class);
        $process     = $this->createMock(Process::class);
        $process->method('isSuccessful')->willReturn(true);
        $afterTested->method('getProcess')->willReturn($process);
        $this->controller->collectFailedTask($afterTested);
        $this->assertCount(0, $this->getLinesPropperty());
        $this->controller->writeCache();
    }

    /**
     * @param string       $suiteName
     * @param string       $featureFile
     * @param array<mixed> $expectedLines
     * @param string|null  $scenarioLine
     *
     * @throws ReflectionException
     *
     * @dataProvider collectFailedProvider
     */
    public function testCollectFailedTask2($suiteName, $featureFile, $expectedLines = [], $scenarioLine = null): void
    {
        $afterTested = $this->createMock(AfterTaskTested::class);
        $process     = $this->createMock(Process::class);
        $process->method('isSuccessful')->willReturn(false);
        $afterTested->method('getProcess')->willReturn($process);

        $task = $this->createMock(TaskEntity::class);
        $afterTested->method('getTask')->willReturn($task);
        $suite = $this->createMock(Suite::class);
        $suite->method('getName')->willReturn($suiteName);
        $feature = $this->createMock(FeatureNode::class);
        $feature->method('getFile')->willReturn($featureFile);

        $task->method('getSuite')->willReturn($suite);
        $task->method('getFeature')->willReturn($feature);

        $scenario = $this->createMock(ScenarioInterface::class);
        if ($scenarioLine) {
            $task->method('getScenario')->willReturn($scenario);
            $scenario->method('getLine')->willReturn($scenarioLine);
        }

        $this->controller->collectFailedTask($afterTested);

        $this->assertCount(1, $this->getLinesPropperty());
        $this->assertEquals($expectedLines, $this->getLinesPropperty());

        $this->controller->writeCache();
    }

    /**
     * @return array<mixed>
     */
    public function collectFailedProvider(): array
    {
        return [
            [
                'some-name',
                'some-file.feature',
                [
                    'some-name' =>
                        [0 => 'some-file.feature:123'],
                ],
                123,
            ],
            [
                'some-name',
                'some-file.feature',

                [
                    'some-name' =>
                        [0 => 'some-file.feature'],
                ],
                null,
            ],
        ];
    }

    /**
     * @return array<mixed>
     *
     * @throws ReflectionException
     */
    private function getLinesPropperty()
    {
        $ref      = new ReflectionClass($this->controller);
        $property = $ref->getProperty('lines');
        $property->setAccessible(true);

        return $property->getValue($this->controller);
    }
}
