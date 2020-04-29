<?php


namespace DMarynicz\BehatParallelExtension\Service;

use Behat\Gherkin\Node\FeatureNode;

class FeatureSpecificationsFinder extends SpecificationsFinder
{
    const SERVICE_ID = 'parallel_extension.feature_specifications_finder';

    /**
     * @param string $path
     * @return array|string[]
     */
    public function findFeatures($path)
    {
        $suites = $this->findSuites($path);
        $features = [];
        foreach ($suites as $suite) {
            foreach ($suite as $feature) {
                /**
                 * @var $feature FeatureNode
                 */
                $features[] = $feature->getFile();
            }
        }

        return $features;
    }
}
