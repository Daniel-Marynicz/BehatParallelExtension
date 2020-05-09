<?php

namespace DMarynicz\BehatParallelExtension\Task;

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
     * @return Task[]
     *
     * @throws ReflectionException
     */
    public function createTasks(InputInterface $input, $path = null)
    {
        $suites = $this->finder->findGroupedSpecifications($path);
        $tasks  = [];
        foreach ($suites as $suite) {
            foreach ($suite as $feature) {
                foreach ($feature->getScenarios() as $scenario) {
                    $testPath = sprintf('%s:%s', $feature->getFile(), $scenario->getLine());
                    $tasks[]  = new Task(
                        $suite->getSuite(),
                        $feature,
                        $testPath,
                        $this->argumentsBuilder->buildArguments($input, $testPath),
                        $scenario
                    );
                }
            }
        }

        return $tasks;
    }
}
