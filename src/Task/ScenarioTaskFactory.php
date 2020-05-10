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
        $specifications = $this->finder->findGroupedSpecifications($path);
        $tasks          = [];
        foreach ($specifications as $spec) {
            foreach ($spec as $feature) {
                foreach ($feature->getScenarios() as $scenario) {
                    $testPath = sprintf('%s:%s', $feature->getFile(), $scenario->getLine());
                    $tasks[]  = new Task(
                        $spec->getSuite(),
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
