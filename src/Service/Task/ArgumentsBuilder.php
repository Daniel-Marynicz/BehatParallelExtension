<?php


namespace DMarynicz\BehatParallelExtension\Service\Task;


use DMarynicz\BehatParallelExtension\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;

class ArgumentsBuilder
{
    const SERVICE_ID = 'parallel_extension.task.arguments_builder';

    /**
     * @param InputInterface $input
     * @param string $path
     * @return string[]
     * @throws \ReflectionException
     */
    public function buildArguments(InputInterface $input, $path)
    {
        $definition = $this->getInputDefinition($input);
        $argv       = [$_SERVER['argv'][0]];

        foreach ($input->getOptions() as $name => $value) {
            if ($value === $definition->getOption($name)->getDefault()) {
                continue;
            }

            if (in_array($name, ['parallel-scenario', 'parallel-feature', 'colors'])) {
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
     * @param InputInterface $input
     * @return InputDefinition
     * @throws \ReflectionException
     */
    private function getInputDefinition(InputInterface $input)
    {
        $ref = new \ReflectionClass($input);
        if (! $ref->hasProperty('definition')) {
            throw new RuntimeException('Input must have definition property');
        }
        $defProp = $ref->getProperty('definition');
        $defProp->setAccessible(true);

        return $defProp->getValue($input);
    }

}