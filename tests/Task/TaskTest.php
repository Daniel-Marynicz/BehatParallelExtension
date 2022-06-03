<?php

namespace DMarynicz\Tests\Task;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Testwork\Suite\Suite;
use DMarynicz\BehatParallelExtension\Task\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    /**
     * @param bool $testWithScenario
     *
     * @dataProvider dataProvider
     */
    public function testTask($testWithScenario): void
    {
        //Suite $suite, FeatureNode $feature, $path, $command = [], Scenario $scenario = null
        $suite   = $this->createMock(Suite::class);
        $feature = $this->createMock(FeatureNode::class);
        $path    = 'some-path';
        $command = ['php', 'ls'];

        $task = new Task($suite, $feature, $path, $command);
        if ($testWithScenario) {
            $scenario = $this->createMock(ScenarioInterface::class);
            $task     = new Task($suite, $feature, $path, $command, $scenario);
        }

        $this->assertEquals(
            $task->getSuite(),
            $this->createMock(Suite::class)
        );

        $this->assertEquals(
            $task->getFeature(),
            $this->createMock(FeatureNode::class)
        );

        $this->assertEquals(
            'some-path',
            $task->getPath()
        );

        $this->assertEquals(
            ['php', 'ls'],
            $task->getCommand()
        );
        $this->assertEquals(
            $testWithScenario ? $this->createMock(ScenarioInterface::class) : null,
            $task->getScenario()
        );
    }

    /**
     * @return bool[][]
     */
    public function dataProvider()
    {
        return [
            [true],
            [false],

        ];
    }
}
