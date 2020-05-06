<?php

namespace DMarynicz\BehatParallelExtension;

use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use DMarynicz\BehatParallelExtension\Cli\RerunController;
use DMarynicz\BehatParallelExtension\Event\WorkerCreated;
use DMarynicz\BehatParallelExtension\Service\FilePutContentsWrapper;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

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
                    ->info('Sets the rerun cache path, must have same value as testers.rerun_cache')
                    ->defaultValue(
                        is_writable(sys_get_temp_dir())
                            ? sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'behat_rerun_cache'
                            : null
                    )
                ->end()
                ->arrayNode('events')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('eventName')
                                ->example(WorkerCreated::WORKER_CREATED)
                                ->cannotBeEmpty()
                            ->end()
                            ->arrayNode('handler')
                                ->info(
                                    'By default class must have method __invoke, '
                                    . 'if array have two elements then second element is the name '
                                    . 'for the method handler name'
                                )
                                ->cannotBeEmpty()
                                ->example([
                                    ['App\Tests\Behat\Event\WorkerCreatedHandler'],
                                    [
                                        'App\Tests\Behat\Event\EventsHandler',
                                        'handleWorkerCreated',
                                    ],

                                ])
                                ->prototype('scalar')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->end()
                ->arrayNode('environments')
                    ->example(
                        [
                            [
                                'CACHE_DIR' => '00-test',
                                'SYMFONY_SERVER_PORT' => 8000,
                                'SYMFONY_SERVER_PID_FILE' => '.web-server-8000-pid',
                                'DATABASE_URL' => 'mysql://db_user:db_password@127.0.0.1:3306/db_name_00?serverVersion=5.7',
                                'SYMFONY_DOTENV_VARS' => '',
                            ],
                            [
                                'CACHE_DIR' => '01-test',
                                'SYMFONY_SERVER_PORT' => 8001,
                                'SYMFONY_SERVER_PID_FILE' => '.web-server-8001-pid',
                                'DATABASE_URL' => 'mysql://db_user:db_password@127.0.0.1:3306/db_name_01?serverVersion=5.7',
                                'SYMFONY_DOTENV_VARS' => '',
                            ],
                            [
                                'CACHE_DIR' => '02-test',
                                'SYMFONY_SERVER_PORT' => 8002,
                                'SYMFONY_SERVER_PID_FILE' => '.web-server-8002-pid',
                                'DATABASE_URL' => 'mysql://db_user:db_password@127.0.0.1:3306/db_name_02?serverVersion=5.7',
                                'SYMFONY_DOTENV_VARS' => '',
                            ],
                            [
                                'CACHE_DIR' => '03-test',
                                'SYMFONY_SERVER_PORT' => 8003,
                                'SYMFONY_SERVER_PID_FILE' => '.web-server-8003-pid',
                                'DATABASE_URL' => 'mysql://db_user:db_password@127.0.0.1:3306/db_name_03?serverVersion=5.7',
                                'SYMFONY_DOTENV_VARS' => '',
                            ],
                        ]
                    )
                    ->prototype('variable')
                ->end()
            ->end();
    }

    /**
     * @param mixed[] $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $locator = new FileLocator(__DIR__ . '/Resources/config');
        $loader  = new YamlFileLoader($container, $locator);
        $loader->load('services.yaml');
    }

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
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
