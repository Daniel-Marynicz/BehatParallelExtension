<?php

namespace DMarynicz\BehatParallelExtension\Task;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Testwork\Suite\Suite;
use ReflectionException;
use Symfony\Component\Console\Input\InputInterface;

final class FeatureTaskFactory implements TaskFactory
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
        $suites    = $this->finder->findGroupedSpecifications($path);
        $tasks     = [];
        $chunkSize = max((int) $input->getOption('parallel-chunk-size'), 1);
        foreach ($suites as $suite) {
            $tasks = array_merge($tasks, ChunkBuilder::buildChunks(
                $suite,
                $chunkSize,
                function (array $chunk) use ($input, $suite) {
                    return $this->createTaskFromChunk($input, $suite->getSuite(), $chunk);
                }
            ));
        }

        return $tasks;
    }

    /**
     * @param FeatureNode[] $chunk
     */
    private function createTaskFromChunk(InputInterface $input, Suite $suite, array $chunk): Task
    {
        $units = array_map(static function (FeatureNode $feature) {
            return new TaskUnit($feature);
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
