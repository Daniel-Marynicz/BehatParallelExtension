<?php


namespace DMarynicz\BehatParallelExtension\Queue;


use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioLikeInterface as Scenario;
use Behat\Testwork\Specification\SpecificationIterator;
use Behat\Testwork\Suite\Suite;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Testwork\Tester\Setup\Teardown;

class ScenarioTask
{
    /**
     * @var SpecificationIterator
     */
    private $specification;

    /**
     * @var FeatureNode
     */
    private $feature;

    /**
     * @var Scenario
     */
    private $scenario;

    /**
     * @var TestResult|null
     */
    private $result;
    /**
     * @var Teardown|null
     */
    private $teardown;

    /**
     * @var array|null
     */
    private $command;

    /**
     * @param SpecificationIterator $specification
     * @param FeatureNode $feature
     * @param Scenario $scenario
     */
    public function __construct(SpecificationIterator $specification, FeatureNode $feature, Scenario $scenario)
    {
        $this->specification = $specification;
        $this->feature = $feature;
        $this->scenario = $scenario;
    }

    /**
     * @return SpecificationIterator
     */
    public function getSpecificationIterator()
    {
        return $this->specification;
    }

    /**
     * @return Suite
     */
    public function getSuite()
    {
        return $this->specification->getSuite();
    }

    /**
     * @return FeatureNode
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * @return Scenario
     */
    public function getScenario()
    {
        return $this->scenario;
    }

    /**
     * @return TestResult
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param TestResult $result
     * @return ScenarioTask
     */
    public function setResult(TestResult $result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @return Teardown
     */
    public function getTeardown()
    {
        return $this->teardown;
    }

    /**
     * @param Teardown $teardown
     * @return ScenarioTask
     */
    public function setTeardown(Teardown $teardown)
    {
        $this->teardown = $teardown;
        return $this;
    }

    /**
     * @return string
     */
    public function getTestPath()
    {
        return sprintf('%s:%s', $this->feature->getFile(), $this->scenario->getLine());
    }

    /**
     * @return array|null
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param array|null $command
     * @return ScenarioTask
     */
    public function setCommand($command)
    {
        $this->command = $command;
        return $this;
    }
}
