<?php

namespace DMarynicz\Tests\Task;

use DMarynicz\BehatParallelExtension\Task\ScenarioTaskFactory;
use DMarynicz\BehatParallelExtension\Task\Task;
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

        $expected = [];
        foreach ($specsToMock as $suiteToMock) {
            $suite = $this->createSuiteMock($suiteToMock);
            foreach ($suiteToMock['features'] as $featureToMock) {
                $feature = $this->createFeatureMock($featureToMock);
                foreach ($featureToMock['scenarios'] as $scenarioToMock) {
                    $scenario   = $this->createScenarioMock($scenarioToMock);
                    $testPath   = sprintf('%s:%s', $featureToMock['file'], $scenarioToMock['line']);
                    $expected[] = new Task(
                        $suite,
                        $feature,
                        $testPath,
                        ['some', 'args'],
                        $scenario
                    );
                }
            }
        }

        $factory =  new ScenarioTaskFactory($finder, $argumentsBuilder);
        $actual  = $factory->createTasks($input, 'some-path');
        $this->assertEquals($expected, $actual);
    }
}
