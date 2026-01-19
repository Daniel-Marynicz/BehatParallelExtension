<?php

namespace DMarynicz\BehatParallelExtension\Task;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioLikeInterface as Scenario;

final class TaskUnit
{
    /** @var FeatureNode */
    private $feature;

    /** @var Scenario|null */
    private $scenario;

    public function __construct(FeatureNode $feature, ?Scenario $scenario = null)
    {
        $this->feature  = $feature;
        $this->scenario = $scenario;
    }

    /**
     * @return FeatureNode
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * @return Scenario|null
     */
    public function getScenario()
    {
        return $this->scenario;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        $path = (string) $this->feature->getFile();

        if ($this->scenario !== null) {
            $path .= ':' . $this->scenario->getLine();
        }

        return $path;
    }
}
