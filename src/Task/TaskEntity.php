<?php

namespace DMarynicz\BehatParallelExtension\Task;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioLikeInterface as Scenario;
use Behat\Testwork\Suite\Suite;

interface TaskEntity
{
    /**
     * @return Suite
     */
    public function getSuite();

    /**
     * @return FeatureNode
     */
    public function getFeature();

    /**
     * @return Scenario|null
     */
    public function getScenario();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return string[]
     */
    public function getCommand();
}
