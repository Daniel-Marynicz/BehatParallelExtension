<?php

namespace DMarynicz\Tests\Behat\Extension;

use Behat\MinkExtension\ServiceContainer\MinkExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use DMarynicz\Tests\Behat\Mink\Driver\ServerLessFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use UnexpectedValueException;

class ServerLessExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'server_less_extension';
    }

    /**
     * Initializes other extensions.
     *
     * This method is called immediately after all extensions are activated but
     * before any extension `configure()` method is called. This allows extensions
     * to hook into the configuration of other extensions providing such an
     * extension point.
     */
    public function initialize(ExtensionManager $extensionManager)
    {
        $minkExtension = $extensionManager->getExtension('mink');
        if (! $minkExtension instanceof MinkExtension) {
            throw new UnexpectedValueException('Expected MinkExtension');
        }

        $minkExtension->registerDriverFactory(new ServerLessFactory());
    }

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
    }

    /**
     * @inheritDoc
     */
    public function configure(ArrayNodeDefinition $builder)
    {
    }

    /**
     * @inheritDoc
     */
    public function load(ContainerBuilder $container, array $config)
    {
    }
}
