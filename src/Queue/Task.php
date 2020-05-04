<?php


namespace DMarynicz\BehatParallelExtension\Queue;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioLikeInterface as Scenario;
use Behat\Testwork\EventDispatcher\Event\AfterTested;
use Behat\Testwork\Suite\Suite;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Testwork\Tester\Setup\Teardown;

class Task
{
    /**
     * @var Suite
     */
    private $suite;

    /**
     * @var FeatureNode
     */
    private $feature;

    /**
     * @var string
     */
    private $path;

    /**
     * @var Scenario|null
     */
    private $scenario;

    /**
     * @var string[]
     */
    private $command;

    /**
     * @var string|null
     */
    private $cwd;

    /**
     * @var array|null
     */
    private $env;

    /**
     * @param Suite $suite
     * @param FeatureNode $feature
     * @param Scenario $scenario
     * @param string $path
     */
    public function __construct(Suite $suite, FeatureNode $feature, $path, Scenario $scenario = null)
    {
        $this->suite = $suite;
        $this->feature = $feature;
        $this->path = $path;
        $this->scenario = $scenario;
        $this->path = $path;
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
     * @return Task
     */
    public function setCommand($command)
    {
        $this->command = $command;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCwd()
    {
        return $this->cwd;
    }

    /**
     * @return array|null
     */
    public function getEnv()
    {
        return $this->env;
    }
}
