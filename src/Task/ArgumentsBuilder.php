<?php

namespace DMarynicz\BehatParallelExtension\Task;

use DMarynicz\BehatParallelExtension\Exception\Runtime;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Process\PhpExecutableFinder;

class ArgumentsBuilder
{
    const SERVICE_ID = 'parallel_extension.task.arguments_builder';

    /**
     * @param string $path
     *
     * @return string[]
     *
     * @throws ReflectionException
     */
    public function buildArguments(InputInterface $input, $path)
    {
        $definition = $this->getInputDefinition($input);
        $phpFinder  = new PhpExecutableFinder();
        $php        = $phpFinder->find();
        if ($php === false) {
            throw new Runtime('Unable to find the PHP executable.');
        }

        if (! defined('BEHAT_BIN_PATH')) {
            throw new Runtime('constant BEHAT_BIN_PATH is not defined.');
        }

        $argv = [$php, BEHAT_BIN_PATH];

        foreach ($input->getOptions() as $name => $value) {
            if ($value === $definition->getOption($name)->getDefault()) {
                continue;
            }

            if (in_array($name, ['parallel', 'parallel-feature', 'colors'])) {
                continue;
            }

            if ($input->getOption('no-colors')  === false) {
                $argv[] = '--colors';
            }

            switch (gettype($value)) {
                case 'boolean':
                    if ($value) {
                        $argv[] = '--' . $name;
                    }

                    break;
                case 'integer':
                case 'double':
                case 'string':
                    $argv[] = '--' . $name;
                    $argv[] = $value;
                    break;
                case 'array':
                    foreach ($value as $valueContent) {
                        $argv[] = '--' . $name;
                        if (! $valueContent) {
                            continue;
                        }

                        $argv[] = $valueContent;
                    }

                    break;
            }
        }

        foreach ($input->getArguments() as $name => $value) {
            if ($value === $definition->getArgument($name)->getDefault()) {
                continue;
            }

            if ($name === 'paths') {
                continue;
            }

            $argv[] = $value;
        }

        $argv[] = $path;

        return $argv;
    }

    /**
     * @return InputDefinition
     *
     * @throws ReflectionException
     */
    private function getInputDefinition(InputInterface $input)
    {
        $ref = new ReflectionClass($input);
        if (! $ref->hasProperty('definition')) {
            throw new Runtime('Input must have definition property');
        }

        $defProp = $ref->getProperty('definition');
        $defProp->setAccessible(true);

        return $defProp->getValue($input);
    }
}
