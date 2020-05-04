<?php

namespace DMarynicz\BehatParallelExtension\Task;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioLikeInterface as Scenario;
use Behat\Testwork\Suite\Suite;

class Task
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
     * @param string $path
     */
    public function __construct(Suite $suite, FeatureNode $feature, $path, Scenario $scenario = null)
    {
        $this->suite    = $suite;
        $this->feature  = $feature;
        $this->path     = $path;
        $this->scenario = $scenario;
        $this->path     = $path;
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

    /**
     * @param string[] $command
     *
     * @return Task
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }
}
