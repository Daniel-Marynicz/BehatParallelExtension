<?php

declare(strict_types=1);

namespace DMarynicz\Tests\Behat\Extension;

use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Composer\InstalledVersions;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Skips tagged features when the installed Behat version is below required minimums.
 */
final class TestsSkipperExtension implements Extension
{
    private const VERSION_TAG_RULES = [
        ['min_version' => '3.23.0', 'tag' => 'skip_without_multiple_paths'],
    ];

    public function getConfigKey(): string
    {
        return 'skip_without_multiple_paths';
    }

    public function initialize(ExtensionManager $extensionManager): void
    {
    }

    public function configure(ArrayNodeDefinition $builder): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config): void
    {
    }

    public function process(ContainerBuilder $container): void
    {
        if (! InstalledVersions::isInstalled('behat/behat')) {
            return;
        }

        $installedVersion = InstalledVersions::getVersion('behat/behat');
        if ($installedVersion === null) {
            return;
        }

        if (! $container->hasParameter('suite.configurations')) {
            return;
        }

        $tagExpressions = $this->collectTagExpressions($installedVersion);
        if (! $tagExpressions) {
            return;
        }

        $suites = (array) $container->getParameter('suite.configurations');

        foreach ($suites as $name => $suite) {
            $settings = $suite['settings'] ?? [];
            $filters  = $settings['filters'] ?? [];
            $existing = $filters['tags'] ?? null;

            $filters['tags'] = $this->mergeTagExpressions($existing, $tagExpressions);

            $settings['filters'] = $filters;
            $suite['settings']   = $settings;
            $suites[$name]       = $suite;
        }

        $container->setParameter('suite.configurations', $suites);
    }

    /**
     * @return string[]
     */
    private function collectTagExpressions(string $installedVersion): array
    {
        $expressions = [];

        foreach (self::VERSION_TAG_RULES as $rule) {
            $minVersion = $rule['min_version'];
            if (! version_compare($installedVersion, $minVersion, '<')) {
                continue;
            }

            $tag           = ltrim($rule['tag'], '@');
            $expressions[] = '~@' . $tag;
        }

        return $expressions;
    }

    /**
     * @param string[] $expressions
     */
    private function mergeTagExpressions(?string $existing, array $expressions): string
    {
        $unique = [];

        if ($existing) {
            $unique[] = $existing;
        }

        foreach ($expressions as $expression) {
            $alreadyPresent = false;
            foreach ($unique as $current) {
                if (strpos($current, $expression) !== false) {
                    $alreadyPresent = true;
                    break;
                }
            }

            if ($alreadyPresent) {
                continue;
            }

            $unique[] = $expression;
        }

        return implode('&&', $unique);
    }
}
