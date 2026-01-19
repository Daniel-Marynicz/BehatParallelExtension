<?php

namespace DMarynicz\Tests;

use Behat\Testwork\ServiceContainer\ExtensionManager;
use DMarynicz\BehatParallelExtension\Extension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ExtensionTest extends TestCase
{
    public function test(): void
    {
        $extension = new Extension();
        $this->assertEquals('parallel_extension', $extension->getConfigKey());
        $extension->initialize(new ExtensionManager([], null));
        $arrayBuilder = $this->createMock(ArrayNodeDefinition::class);
        $nodeBuilder  = $this->createMock(NodeBuilder::class);
        $nodeBuilder->method('append')->willReturn($nodeBuilder);
        $nodeBuilder->method('end')->willReturn($arrayBuilder);
        $arrayBuilder
            ->method('children')
            ->willReturn($nodeBuilder);
        $extension->configure($arrayBuilder);

        $containerBuilder = $this->createMock(ContainerBuilder::class);
        $containerBuilder->method('findDefinition')->willReturn($this->createMock(Definition::class));
        $extension->load($containerBuilder, ['environments' => []]);
        $extension->process($this->createMock(ContainerBuilder::class));
    }
}
