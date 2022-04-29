<?php

namespace DMarynicz\Tests\Cli;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioLikeInterface;
use Behat\Testwork\Cli\Controller;
use DMarynicz\BehatParallelExtension\Cli\ParallelController;
use DMarynicz\BehatParallelExtension\Event\AfterTaskTested;
use DMarynicz\BehatParallelExtension\Event\BeforeTaskTested;
use DMarynicz\BehatParallelExtension\Event\EventDispatcherDecorator;
use DMarynicz\BehatParallelExtension\Task\Queue;
use DMarynicz\BehatParallelExtension\Task\TaskEntity;
use DMarynicz\BehatParallelExtension\Task\TaskFactory;
use DMarynicz\BehatParallelExtension\Util\CanDetermineNumberOfProcessingUnits;
use DMarynicz\BehatParallelExtension\Worker\Poll;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;
use ReflectionProperty;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Process\Process;

abstract class ParallelControllerTest extends ControllerTest
{
    /** @var ParallelController&Controller */
    protected $controller;
    /** @var MockObject |TaskFactory */
    protected $taskFactory;
    /** @var MockObject |Poll */
    protected $poll;
    /** @var MockObject |Queue */
    protected $queue;
    /** @var MockObject | EventDispatcherDecorator */
    protected $eventDispatcherDecorator;

    /** @var MockObject | CanDetermineNumberOfProcessingUnits */
    protected $numberOfCores;

    abstract public function testExecute();

    protected function setUp(): void
    {
        parent::setUp();
        $this->taskFactory              = $this->createMock(TaskFactory::class);
        $this->poll                     = $this->createMock(Poll::class);
        $this->queue                    = $this->createMock(Queue::class);
        $this->eventDispatcherDecorator = $this->createMock(EventDispatcherDecorator::class);
        $this->numberOfCores            = $this->createMock(CanDetermineNumberOfProcessingUnits::class);
        $outputFormatter                = $this->createMock(OutputFormatterInterface::class);
        $outputFormatter->method('isDecorated')->willReturn(false);
        $this
            ->output
            ->method('getFormatter')
            ->willReturn(
                $outputFormatter
            );
    }

    public function testConfigure()
    {
        $this->decoratedController->expects($this->once())->method('configure')->with($this->command);
        $this->command->method('addOption')->willReturn($this->command);
        $this->command->method('addUsage')->willReturn($this->command);
        $this->controller->configure($this->command);
    }

    /**
     * @param string      $featureTitle
     * @param string|null $scenarioTitle
     * @param string      $expectedFeatureTitle
     * @param string|null $expectedScenarioTitle
     *
     * @dataProvider beforeTaskTestedProvider
     */
    public function testBeforeTaskTested($featureTitle, $scenarioTitle, $expectedFeatureTitle, $expectedScenarioTitle)
    {
        $beforeTested = $this->createMock(BeforeTaskTested::class);
        $task         = $this->createMock(TaskEntity::class);
        $beforeTested->method('getTask')->willReturn($task);

        $feature = $this->createMock(FeatureNode::class);
        $feature->method('getTitle')->willReturn($featureTitle);

        $task->method('getFeature')->willReturn($feature);

        if ($scenarioTitle) {
            $scenario = $this->createMock(ScenarioLikeInterface::class);
            $scenario->method('getTitle')->willReturn($scenarioTitle);
            $task->method('getScenario')->willReturn($scenario);
        }

        $progressBar = new ProgressBar($this->output);
        $this->setNonAccessibleValue($this->controller, 'progressBar', $progressBar);

        $this->controller->beforeTaskTested($beforeTested);

        $this->assertEquals($expectedFeatureTitle, $progressBar->getMessage('feature'));
        $this->assertEquals($expectedScenarioTitle, $progressBar->getMessage('scenario'));
    }

    public function testParallelTestsAborted()
    {
        $this->poll->expects($this->once())->method('stop');
        $this->controller->parallelTestsAborted();
    }

    /**
     * @return array<array<string|null>>
     */
    public function beforeTaskTestedProvider()
    {
        return [
            [
                'feature title',
                'scenarioTitle',
                '<info>Feature: feature title</info>',
                '<info>Scenario: scenarioTitle</info>',
            ],
            [
                'feature title',
                null,
                '<info>Feature: feature title</info>',
                null,
            ],
        ];
    }

    /**
     * @param bool        $isSuccessful
     * @param string|null $output
     * @param string|null $errorOutput
     * @param string|null $expectedOutput
     * @param bool        $stopOnFailure
     * @param bool        $pollWillBeStopped
     *
     * @throws ReflectionException
     *
     * @dataProvider afterTaskTestedProvider
     */
    public function testAfterTaskTested(
        $isSuccessful,
        $output,
        $errorOutput,
        $expectedOutput,
        $stopOnFailure,
        $pollWillBeStopped
    ) {
        $process = $this->createMock(Process::class);
        $process->method('isSuccessful')->willReturn($isSuccessful);
        $process->method('getOutput')->willReturn($output);
        $process->method('getErrorOutput')->willReturn($errorOutput);

        $progressBar = new ProgressBar(clone $this->output);
        $this->setNonAccessibleValue($this->controller, 'progressBar', $progressBar);

        $this
            ->output
            ->expects($this->exactly($isSuccessful ? 0 : 1))
            ->method('write')
            ->with($expectedOutput);

        $this->setNonAccessibleValue($this->controller, 'output', $this->output);
        $this->setNonAccessibleValue($this->controller, 'input', $this->input);

        $this->input->method('getOption')->with('stop-on-failure')->willReturn($stopOnFailure);
        $this->poll->expects($this->exactly($pollWillBeStopped ? 1 : 0))->method('stop');
        $afterTested = $this->createMock(AfterTaskTested::class);
        $afterTested->method('getProcess')->willReturn($process);

        $this->controller->afterTaskTested($afterTested);
    }

    /**
     * @return array<mixed>
     */
    public function afterTaskTestedProvider()
    {
        return [
            [
                true,
                null,
                null,
                null,
                false,
                false,
            ],
            [
                false,
                "stdout \n output \n",
                "stderror \n output \n",
                "\nstdout \n output \nstderror \n output \n",
                true,
                true,
            ],
            [
                false,
                "stdout \n output \n",
                "stderror \n output \n",
                "\nstdout \n output \nstderror \n output \n",
                false,
                false,
            ],
        ];
    }

    public function testExecuteWithTaskErrors()
    {
        $taskEntity = $this->createMock(TaskEntity::class);
        $this->taskFactory->method('createTasks')->willReturn([$taskEntity]);

        $this->setNonAccessibleValue($this->controller, 'exitCode', 1);

        $result = $this->controller->execute($this->input, $this->output);
        $this->assertEquals(1, $result);
    }

    /**
     * @param mixed  $object
     * @param string $name
     * @param mixed  $value
     *
     * @throws ReflectionException
     */
    private function setNonAccessibleValue($object, $name, $value)
    {
        $inputProp = new ReflectionProperty($object, $name);
        $inputProp->setAccessible(true);
        $inputProp->setValue($object, $value);
    }
}
