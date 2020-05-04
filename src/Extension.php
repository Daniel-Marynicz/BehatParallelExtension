<?php

namespace DMarynicz\BehatParallelExtension;

use Behat\Testwork\Cli\ServiceContainer\CliExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Behat\Testwork\Specification\ServiceContainer\SpecificationExtension;
use Behat\Testwork\Suite\ServiceContainer\SuiteExtension;
use DMarynicz\BehatParallelExtension\Cli\ParallelFeatureController;
use DMarynicz\BehatParallelExtension\Cli\ParallelScenarioController;
use DMarynicz\BehatParallelExtension\Service\Finder\FeatureSpecificationsFinder;
use DMarynicz\BehatParallelExtension\Service\Finder\ScenarioSpecificationsFinder;
use DMarynicz\BehatParallelExtension\Service\Task\ArgumentsBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Behat\Behat\Tester\Cli\RerunController;
use DMarynicz\BehatParallelExtension\Queue\Queue;
use DMarynicz\BehatParallelExtension\Worker\WorkerPoll;

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
        //new Reference(EventDispatcherExtension::DISPATCHER_ID),

        $this->loadSpecificationsFinder(FeatureSpecificationsFinder::class, $container);
        $this->loadSpecificationsFinder(ScenarioSpecificationsFinder::class, $container);
        $this->loadService(ArgumentsBuilder::class, $container);


        $this->loadService(Queue::class, $container);
        $definition = new Definition(
            WorkerPoll::class,
            [
                new Reference(Queue::SERVICE_ID),
                new Reference(EventDispatcherExtension::DISPATCHER_ID)
            ]
        );
        $container->setDefinition(WorkerPoll::SERVICE_ID, $definition);


        $this->loadParallelController(
            ParallelScenarioController::class,
            ScenarioSpecificationsFinder::class,
            $container
        );
       /* $this->loadParallelController(
            ParallelFeatureController::class,
            FeatureSpecificationsFinder::class,
            $container
        );*/
    }

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
    }

    private function loadTaskArgumentsBuilder(ContainerBuilder $container)
    {
        $definition = new Definition(ArgumentsBuilder::class);
        $container->setDefinition(ArgumentsBuilder::SERVICE_ID, $definition);
    }
    /**
     * @param string $className
     * @param ContainerBuilder $container
     */
    private function loadSpecificationsFinder($className, ContainerBuilder $container)
    {
        $definition = new Definition(
            $className,
            [
                new Reference(SuiteExtension::REGISTRY_ID),
                new Reference(SpecificationExtension::FINDER_ID)
            ]
        );

        $container->setDefinition($className::SERVICE_ID, $definition);
    }

    private function loadParallelController($controllerClass, $specificationsFinderClass, ContainerBuilder $container)
    {
        $definition = new Definition(
            $controllerClass,
            [
                new Reference($controllerClass::SERVICE_ID.'.inner'),
                new Reference($specificationsFinderClass::SERVICE_ID),
                new Reference(ArgumentsBuilder::SERVICE_ID),
                new Reference(WorkerPoll::SERVICE_ID),
                new Reference(Queue::SERVICE_ID),
                new Reference(EventDispatcherExtension::DISPATCHER_ID),
  //              new Reference(TesterExtension::EXERCISE_ID)
                //new Reference(ParallelWorkerFactory::class),
            ]
        );

        $definition
            ->setDecoratedService(CliExtension::CONTROLLER_TAG . '.exercise');
        $container->setDefinition($controllerClass::SERVICE_ID, $definition);
    }

    private function loadService($class, ContainerBuilder $container)
    {
        $definition = new Definition(
            $class
        );
        $container->setDefinition($class::SERVICE_ID, $definition);
    }
}

