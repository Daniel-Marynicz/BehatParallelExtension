<?php

namespace DMarynicz\Tests\Task;

use DMarynicz\BehatParallelExtension\Task\FeatureTaskFactory;
use DMarynicz\BehatParallelExtension\Task\Task;
use ReflectionException;

class FeatureTaskFactoryTest extends TaskFactoryTest
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
                    $expected[] = new Task(
                        $suite,
                        $feature,
                        $featureToMock['file'],
                        ['some', 'args'],
                        null
                    );
                }
            }
        }

        $factory =  new FeatureTaskFactory($finder, $argumentsBuilder);
        $actual  = $factory->createTasks($input, 'some-path');
        $this->assertEquals($expected, $actual);
    }
}
