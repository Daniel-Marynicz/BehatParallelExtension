<?php


namespace DMarynicz\BehatParallelExtension\Service;

use Behat\Gherkin\Node\FeatureNode;

class ScenarioSpecificationsFinder extends SpecificationsFinder
{
    const SERVICE_ID = 'parallel_extension.scenario_specifications_finder';

    /**
     * @param string $path
     * @return array|string[]
     */
    public function findScenarios($path)
    {
        $suites = $this->findSuites($path);
        $scenarios = [];
        foreach ($suites as $suite) {
            foreach ($suite as $feature) {
                /**
                 * @var $feature FeatureNode
                 */
                foreach ($feature->getScenarios() as $scenario) {
                    $scenarios[] = sprintf('%s:%s', $feature->getFile(), $scenario->getLine());
                }
            }
        }

        return $scenarios;
    }
}
