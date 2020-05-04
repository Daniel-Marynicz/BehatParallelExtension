<?php

namespace DMarynicz\BehatParallelExtension;

use Behat\Testwork\Cli\ServiceContainer\CliExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Behat\Testwork\Specification\ServiceContainer\SpecificationExtension;
use Behat\Testwork\Suite\ServiceContainer\SuiteExtension;
use DMarynicz\BehatParallelExtension\Cli\ParallelScenarioController;
use DMarynicz\BehatParallelExtension\Cli\RerunController;
use DMarynicz\BehatParallelExtension\Queue\Queue;
use DMarynicz\BehatParallelExtension\Service\FilePutContentsWrapper;
use DMarynicz\BehatParallelExtension\Service\Finder\FeatureSpecificationsFinder;
use DMarynicz\BehatParallelExtension\Service\Finder\ScenarioSpecificationsFinder;
use DMarynicz\BehatParallelExtension\Service\Task\ArgumentsBuilder;
use DMarynicz\BehatParallelExtension\Worker\WorkerPoll;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class Extension implements ExtensionInterface
{
    const JSON_ENCODER_SERVICE_ID = 'parallel_extension.json_encoder';

    /**
     * @return string
     */
    public function getConfigKey()
    {
        return 'parallel_extension';
    }

    /**
     * {@inheritDoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->scalarNode('rerun_cache')
                ->info('Sets the rerun cache path')
                ->defaultValue(
                    is_writable(sys_get_temp_dir())
                        ? sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'behat_rerun_cache'
                        : null
                )
            ->end();
    }

    /**
     * @param mixed[] $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $definition = new Definition(
            JsonEncoder::class
        );
        $container->setDefinition(self::JSON_ENCODER_SERVICE_ID, $definition);

        $this->loadService(FilePutContentsWrapper::class, $container);

        $this->loadSpecificationsFinder(FeatureSpecificationsFinder::class, $container);
        $this->loadSpecificationsFinder(ScenarioSpecificationsFinder::class, $container);
        $this->loadService(ArgumentsBuilder::class, $container);

        $this->loadService(Queue::class, $container);
        $definition = new Definition(
            WorkerPoll::class,
            [
                new Reference(Queue::SERVICE_ID),
                new Reference(EventDispatcherExtension::DISPATCHER_ID),
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

        $this->loadRerunController($container, $config['rerun_cache']);
    }

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
    }

    /**
     * @param string $className
     */
    private function loadSpecificationsFinder($className, ContainerBuilder $container)
    {
        $definition = new Definition(
            $className,
            [
                new Reference(SuiteExtension::REGISTRY_ID),
                new Reference(SpecificationExtension::FINDER_ID),
            ]
        );

        $container->setDefinition($className::SERVICE_ID, $definition);
    }

    /**
     * @param string $controllerClass
     * @param string $specificationsFinderClass
     */
    private function loadParallelController($controllerClass, $specificationsFinderClass, ContainerBuilder $container)
    {
        $definition = new Definition(
            $controllerClass,
            [
                new Reference($controllerClass::SERVICE_ID . '.inner'),
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

    /**
     * @param string $class
     */
    private function loadService($class, ContainerBuilder $container)
    {
        $definition = new Definition(
            $class
        );
        $container->setDefinition($class::SERVICE_ID, $definition);
    }

    /**
     * Loads rerun controller.
     *
     * @param string|null $cachePath
     */
    private function loadRerunController(ContainerBuilder $container, $cachePath)
    {
        $definition = new Definition(RerunController::class, [
            new Reference(EventDispatcherExtension::DISPATCHER_ID),
            new Reference(self::JSON_ENCODER_SERVICE_ID),
            new Reference(FilePutContentsWrapper::SERVICE_ID),
            $cachePath,
            $container->getParameter('paths.base'),
        ]);
        $container->setDefinition(RerunController::SERVICE_ID, $definition);
    }
}
