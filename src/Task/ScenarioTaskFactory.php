<?php

namespace DMarynicz\BehatParallelExtension\Task;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioLikeInterface;
use Behat\Testwork\Suite\Suite;
use ReflectionException;
use Symfony\Component\Console\Input\InputInterface;

final class ScenarioTaskFactory implements TaskFactory
{
    /** @var TestworkSpecificationsFinder */
    private $finder;

    /** @var ArgumentsBuilder */
    private $argumentsBuilder;

    public function __construct(TestworkSpecificationsFinder $finder, ArgumentsBuilder $argumentsBuilder)
    {
        $this->finder           = $finder;
        $this->argumentsBuilder = $argumentsBuilder;
    }

    /**
     * @param string|null $path
     *
     * @return TaskEntity[]
     *
     * @throws ReflectionException
     */
    public function createTasks(InputInterface $input, $path = null): array
    {
        $specifications = $this->finder->findGroupedSpecifications($path);
        $tasks          = [];
        $chunkSize      = max((int) $input->getOption('parallel-chunk-size'), 1);

        foreach ($specifications as $spec) {
            $scenarios = [];
            foreach ($spec as $feature) {
                foreach ($feature->getScenarios() as $scenario) {
                    $scenarios[] = [
                        'feature' => $feature,
                        'scenario' => $scenario,
                    ];
                }
            }

            $tasks = array_merge($tasks, ChunkBuilder::buildChunks(
                $scenarios,
                $chunkSize,
                function (array $chunk) use ($input, $spec) {
                    return $this->createTaskFromChunk($input, $spec->getSuite(), $chunk);
                }
            ));
        }

        return $tasks;
    }

    /**
     * @param array<array{feature: FeatureNode, scenario: ScenarioLikeInterface}> $chunk
     */
    private function createTaskFromChunk(InputInterface $input, Suite $suite, array $chunk): Task
    {
        $units = array_map(static function (array $item) {
            return new TaskUnit($item['feature'], $item['scenario']);
        }, $chunk);

        $paths = array_map(static function (TaskUnit $unit) {
            return $unit->getPath();
        }, $units);

        return new Task(
            $suite,
            $units,
            $this->argumentsBuilder->buildArguments($input, $paths)
        );
    }
}
