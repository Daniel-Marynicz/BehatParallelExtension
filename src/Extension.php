<?php

namespace DMarynicz\BehatParallelExtension;

use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use DMarynicz\BehatParallelExtension\Event\WorkerCreated;
use DMarynicz\BehatParallelExtension\Exception\UnexpectedValue;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

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
                ->append($this->addEventsNode())
                ->append($this->addEnvironmentsNode())
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
     * @return ArrayNodeDefinition
     */
    private function addEventsNode()
    {
        $node = $this->getNewArrayNode('events');

        // @phpstan-ignore-next-line
        $node
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
            ->end();

        return $node;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function addEnvironmentsNode()
    {
        $node = $this->getNewArrayNode('environments');

        $node
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
        ->end();

        return $node;
    }

    /**
     * @param string $name
     *
     * @return ArrayNodeDefinition
     */
    private function getNewArrayNode($name)
    {
        if (method_exists(TreeBuilder::class, 'root')) {
            // @phpstan-ignore-next-line
            $treeBuilder = new TreeBuilder();

            // @phpstan-ignore-next-line
            $node = $treeBuilder->root($name);
            if (! $node instanceof ArrayNodeDefinition) {
                throw new UnexpectedValue('expected ArrayNodeDefinition');
            }

            return $node;
        }

        $treeBuilder = new TreeBuilder($name);

        $node = $treeBuilder->getRootNode();

        if (! $node instanceof ArrayNodeDefinition) {
            throw new UnexpectedValue('expected ArrayNodeDefinition');
        }

        return $node;
    }
}
