<?php

namespace DMarynicz\Tests\Task;

use DMarynicz\BehatParallelExtension\Task\ScenarioTaskFactory;
use DMarynicz\BehatParallelExtension\Task\Task;
use ReflectionException;

class ChunkedScenarioTaskFactoryTest extends TaskFactoryTest
{
    /**
     * @throws ReflectionException
     */
    public function testCreateChunkedTasks(): void
    {
        $specsToMock = [
            [
                'name' => 'suite01',
                'features' => [
                    [
                        'name' => 'f1',
                        'file' => 'f1.feature',
                        'scenarios' => [
                            ['name' => 's1', 'line' => 1],
                            ['name' => 's2', 'line' => 2],
                            ['name' => 's3', 'line' => 3],
                        ],
                    ],
                ],
            ],
        ];

        $finder           = $this->createSpecificationsFinderMock($specsToMock);
        $argumentsBuilder = $this->createArgumentsBuilderMock();
        $argumentsBuilder->method('buildArguments')->willReturnCallback(static function ($input, $paths) {
            return array_merge(['behat'], $paths);
        });

        $input = $this->createInputInterfaceMock();
        $input->method('getOption')->willReturnMap([
            ['parallel-chunk-size', 2],
            ['rerun', false],
        ]);

        $factory = new ScenarioTaskFactory($finder, $argumentsBuilder);
        $tasks   = $factory->createTasks($input);

        $this->assertCount(2, $tasks);

        $this->assertInstanceOf(Task::class, $tasks[0]);
        $this->assertEquals(['f1.feature:1', 'f1.feature:2'], $tasks[0]->getPaths());
        $this->assertEquals(['behat', 'f1.feature:1', 'f1.feature:2'], $tasks[0]->getCommand());
        $this->assertCount(2, $tasks[0]->getUnits());

        $this->assertInstanceOf(Task::class, $tasks[1]);
        $this->assertEquals(['f1.feature:3'], $tasks[1]->getPaths());
        $this->assertEquals(['behat', 'f1.feature:3'], $tasks[1]->getCommand());
        $this->assertCount(1, $tasks[1]->getUnits());
    }
}
