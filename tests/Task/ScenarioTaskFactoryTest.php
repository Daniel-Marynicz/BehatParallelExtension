<?php

namespace DMarynicz\Tests\Task;

use DMarynicz\BehatParallelExtension\Task\ScenarioTaskFactory;
use DMarynicz\BehatParallelExtension\Task\Task;
use DMarynicz\BehatParallelExtension\Task\TaskUnit;
use ReflectionException;

class ScenarioTaskFactoryTest extends TaskFactoryTest
{
    /**
     * @param array<mixed> $specsToMock
     *
     * @throws ReflectionException
     *
     * @dataProvider createTasksProvider
     */
    public function testCreateTasks($specsToMock): void
    {
        $finder           = $this->createSpecificationsFinderMock($specsToMock);
        $argumentsBuilder = $this->createArgumentsBuilderMock();
        $argumentsBuilder->method('buildArguments')->willReturn(['some', 'args']);
        $input = $this->createInputInterfaceMock();
        $input->method('getOption')->willReturnMap([
            ['parallel-chunk-size', 1],
            ['rerun', false],
        ]);

        $expected = [];
        foreach ($specsToMock as $suiteToMock) {
            $suite = $this->createSuiteMock($suiteToMock);
            foreach ($suiteToMock['features'] as $featureToMock) {
                $feature = $this->createFeatureMock($featureToMock);
                foreach ($featureToMock['scenarios'] as $scenarioToMock) {
                    $scenario   = $this->createScenarioMock($scenarioToMock);
                    $expected[] = new Task(
                        $suite,
                        [new TaskUnit($feature, $scenario)],
                        ['some', 'args']
                    );
                }
            }
        }

        $factory =  new ScenarioTaskFactory($finder, $argumentsBuilder);
        $actual  = $factory->createTasks($input, 'some-path');
        $this->assertEquals($expected, $actual);
    }
}
