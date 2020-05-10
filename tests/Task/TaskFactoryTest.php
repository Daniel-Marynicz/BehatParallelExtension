<?php

namespace DMarynicz\Tests\Task;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Testwork\Specification\SpecificationIterator;
use Behat\Testwork\Suite\Suite;
use DMarynicz\BehatParallelExtension\Task\ArgumentsBuilder;
use DMarynicz\BehatParallelExtension\Task\TestworkSpecificationsFinder;
use DMarynicz\Tests\MockIterator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;

abstract class TaskFactoryTest extends TestCase
{
    use MockIterator;

    /**
     * @return array<mixed>
     */
    public function createTasksProvider()
    {
        return [
            [
                [
                    'suite' => [
                        'name' => 'suite name',
                        'features' => [
                            [
                                'name' => 'feature name',
                                'file' => 'some-file.feature',
                                'scenarios' => [
                                    [
                                        'name' => 'some-name',
                                        'line' => 123,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                [
                    'suite' => [
                        'name' => 'suite name',
                        'features' => [
                            [
                                'name' => 'feature name',
                                'file' => 'some-file.feature',
                                'scenarios' => [
                                    [
                                        'name' => 'some-name',
                                        'line' => 123,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'suite' => [
                        'name' => 'suite name 2',
                        'features' => [
                            [
                                'name' => 'feature name',
                                'file' => 'some-file.feature',
                                'scenarios' => [
                                    [
                                        'name' => 'some-name',
                                        'line' => 123,
                                    ],
                                    [
                                        'name' => '23-name',
                                        'line' => 12223,
                                    ],
                                    [
                                        'name' => 'some-name',
                                        'line' => 123333,
                                    ],
                                ],
                            ],
                            [
                                'name' => 'feat3ure name',
                                'file' => 'some-file2.feature',
                                'scenarios' => [
                                    [
                                        'name' => 'some-name',
                                        'line' => 123,
                                    ],
                                ],
                            ],
                            [
                                'name' => 'feature2 name',
                                'file' => 'some-file4.feature',
                                'scenarios' => [
                                    [
                                        'name' => 'some-name',
                                        'line' => 123,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<mixed> $specsToMock
     *
     * @return TestworkSpecificationsFinder|MockObject
     */
    protected function createSpecificationsFinderMock($specsToMock)
    {
        $specifications = [];
        foreach ($specsToMock as $suiteToMock) {
            $iterator = $this->createMock(SpecificationIterator::class);

            $suite = $this->createSuiteMock($suiteToMock);
            $iterator->method('getSuite')->willReturn($suite);

            $features = [];
            foreach ($suiteToMock['features'] as $featureToMock) {
                $features[] = $this->createFeatureMock($featureToMock);
            }

            $this->mockIteratorItems($iterator, $features);
            $specifications[] = $iterator;
        }

        $mock = $this->createMock(TestworkSpecificationsFinder::class);
        $mock
            ->method('findGroupedSpecifications')
            ->willReturn($specifications);

        return $mock;
    }

    /**
     * @param array<mixed> $suiteToMock
     *
     * @return Suite|MockObject
     */
    protected function createSuiteMock($suiteToMock)
    {
        $mock = $this->createMock(Suite::class);
        $mock->method('getName')->willReturn($suiteToMock['name']);

        return $mock;
    }

    /**
     * @param array<mixed> $featureToMock
     *
     * @return FeatureNode|MockObject
     */
    protected function createFeatureMock($featureToMock)
    {
        $feature = $this->createMock(FeatureNode::class);
        $feature->method('getFile')->willReturn($featureToMock['file']);
        $scenarios = [];
        foreach ($featureToMock['scenarios'] as $scenarioToMock) {
            $scenarios[] = $this->createScenarioMock($scenarioToMock);
        }

        $feature->method('getScenarios')->willReturn($scenarios);

        return $feature;
    }

    /**
     * @param array<mixed> $scenarioToMock
     *
     * @return ScenarioInterface|MockObject
     */
    protected function createScenarioMock($scenarioToMock)
    {
        $scenario = $this->createMock(ScenarioInterface::class);
        $scenario->method('getLine')->willReturn($scenarioToMock['line']);

        return $scenario;
    }

    /**
     * @return ArgumentsBuilder|MockObject
     */
    protected function createArgumentsBuilderMock()
    {
        return $this->createMock(ArgumentsBuilder::class);
    }

    /**
     * @return MockObject|InputInterface
     */
    protected function createInputInterfaceMock()
    {
        return $this->createMock(InputInterface::class);
    }
}
