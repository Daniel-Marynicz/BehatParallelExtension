<?php

namespace DMarynicz\BehatParallelExtension\Finder;

use DMarynicz\BehatParallelExtension\Service\Finder\SpecificationsFinder;
use DMarynicz\BehatParallelExtension\Task\Task;

class ScenarioSpecificationsFinder extends SpecificationsFinder
{
    const SERVICE_ID = 'parallel_extension.finder.scenario_specifications_finder';

    /**
     * @param string|null $path
     *
     * @return array|Task[]
     */
    public function findScenarios($path)
    {
        $suites = $this->findGroupedSpecifications($path);
        $tasks  = [];
        foreach ($suites as $suite) {
            foreach ($suite as $feature) {
                foreach ($feature->getScenarios() as $scenario) {
                    $tasks[] = new Task(
                        $suite->getSuite(),
                        $feature,
                        sprintf('%s:%s', $feature->getFile(), $scenario->getLine()),
                        [],
                        $scenario
                    );
                }
            }
        }

        return $tasks;
    }
}
