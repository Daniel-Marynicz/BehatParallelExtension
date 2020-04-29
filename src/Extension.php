<?php

namespace DMarynicz\BehatParallelExtension;

use Behat\Testwork\Cli\ServiceContainer\CliExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Behat\Testwork\Specification\ServiceContainer\SpecificationExtension;
use Behat\Testwork\Suite\ServiceContainer\SuiteExtension;
use DMarynicz\BehatParallelExtension\Cli\ParallelScenarioController;
use DMarynicz\BehatParallelExtension\Service\FeatureSpecificationsFinder;
use DMarynicz\BehatParallelExtension\Service\ScenarioSpecificationsFinder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class Extension implements ExtensionInterface
{
    public function getConfigKey()
    {
        return 'parallel_extension';
    }

    /**
     * @inheritDoc
     */
    public function initialize(ExtensionManager $extensionManager)
    {
    }

    /**
     * @inheritDoc
     */
    public function configure(ArrayNodeDefinition $builder)
    {
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $this->loadSpecificationsFinder(FeatureSpecificationsFinder::class, $container);
        $this->loadSpecificationsFinder(ScenarioSpecificationsFinder::class, $container);


        $this->loadParallelController(
            ParallelScenarioController::class,
            FeatureSpecificationsFinder::class,
            $container
        );
        $this->loadParallelController(
            ParallelScenarioController::class,
            ScenarioSpecificationsFinder::class,
            $container
        );
    }

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
    }

    /**
     * @param string $class
     * @param ContainerBuilder $container
     */
    private function loadSpecificationsFinder($className, ContainerBuilder $container)
    {
        $definition = new Definition(
            $className,
            [
                SuiteExtension::REGISTRY_ID,
                SpecificationExtension::FINDER_ID
            ]
        );

        $container->setDefinition($className::SERVICE_ID, $definition);
    }

    private function loadParallelController($controllerClass, $specificationsFinderClass, ContainerBuilder $container)
    {
        $definition = new Definition(
            $controllerClass,
            [
                new Reference(CliExtension::CONTROLLER_TAG . '.parallel_exercise.inner'),
                new Reference($specificationsFinderClass::SERVICE_ID),
  //              new Reference(TesterExtension::EXERCISE_ID)
                //new Reference(ParallelWorkerFactory::class),
            ]
        );

        $definition
            ->setDecoratedService(CliExtension::CONTROLLER_TAG . '.exercise');
        $container->setDefinition($controllerClass::SERVICE_ID, $definition);
    }
}

