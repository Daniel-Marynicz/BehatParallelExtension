<?php

namespace DMarynicz\Tests\Task;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Testwork\Suite\Suite;
use DMarynicz\BehatParallelExtension\Task\Task;
use DMarynicz\BehatParallelExtension\Task\TaskUnit;
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
        $suite   = $this->createMock(Suite::class);
        $feature = $this->createMock(FeatureNode::class);
        $feature->method('getFile')->willReturn('some-path');

        if ($testWithScenario) {
            $scenario = $this->createMock(ScenarioInterface::class);
            $scenario->method('getLine')->willReturn(123);
            $units = [new TaskUnit($feature, $scenario)];
        } else {
            $units = [new TaskUnit($feature)];
        }

        $command = ['php', 'ls'];
        $task    = new Task($suite, $units, $command);

        $this->assertSame($suite, $task->getSuite());

        $this->assertEquals(
            $testWithScenario ? ['some-path:123'] : ['some-path'],
            $task->getPaths()
        );

        $this->assertEquals(['php', 'ls'], $task->getCommand());

        $this->assertSame($units, $task->getUnits());
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
