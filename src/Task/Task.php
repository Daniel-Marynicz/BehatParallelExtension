<?php

namespace DMarynicz\BehatParallelExtension\Task;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioLikeInterface as Scenario;
use Behat\Testwork\Suite\Suite;

final class Task implements TaskEntity
{
    /** @var Suite */
    private $suite;

    /** @var FeatureNode */
    private $feature;

    /** @var string */
    private $path;

    /** @var Scenario|null */
    private $scenario;

    /** @var string[] */
    private $command;

    /**
     * @param string   $path
     * @param string[] $command
     */
    public function __construct(Suite $suite, FeatureNode $feature, $path, $command = [], Scenario $scenario = null)
    {
        $this->suite    = $suite;
        $this->feature  = $feature;
        $this->path     = $path;
        $this->command  = $command;
        $this->scenario = $scenario;
    }

    /**
     * @return Suite
     */
    public function getSuite()
    {
        return $this->suite;
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
        return $this->path;
    }

    /**
     * @return string[]
     */
    public function getCommand()
    {
        return $this->command;
    }
}
