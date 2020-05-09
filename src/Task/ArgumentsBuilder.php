<?php

namespace DMarynicz\BehatParallelExtension\Task;

use DMarynicz\BehatParallelExtension\Exception\Runtime;
use ReflectionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Process\PhpExecutableFinder;

final class ArgumentsBuilder
{
    /** @var PhpExecutableFinder */
    private $phpFinder;

    public function __construct(PhpExecutableFinder $phpFinder)
    {
        $this->phpFinder = $phpFinder;
    }

    /**
     * @param string $path
     *
     * @return string[]
     *
     * @throws ReflectionException
     */
    public function buildArguments(InputInterface $input, $path)
    {
        $arguments = $this->buildFirstArguments();

        $arguments = array_merge($arguments, $this->buildOptionArguments($input));
        $arguments = array_merge($arguments, $this->buildRemainingArguments($input));

        $arguments[] = $path;

        return $arguments;
    }

    /**
     * @return string[]
     */
    private function buildFirstArguments()
    {
        return [$this->getPHPBin(), $this->getBehatBin()];
    }

    /**
     * @return string[]
     */
    private function buildOptionArguments(InputInterface $input)
    {
        $arguments = [];

        if ($input->getOption('no-interaction')  === false) {
            $arguments[] = '--no-interaction';
        }

        if ($input->getOption('fail-on-undefined-step')  === false) {
            $arguments[] = '--fail-on-undefined-step';
        }

        foreach ($input->getOptions() as $name => $value) {
            if (in_array($name, ['parallel', 'parallel-feature'])) {
                continue;
            }

            $arguments = array_merge($arguments, $this->getArgumentsFromInputValue($name, $value));
        }

        return $arguments;
    }

    /**
     * @return string[]
     *
     * @throws ReflectionException
     */
    private function buildRemainingArguments(InputInterface $input)
    {
        $arguments = [];
        foreach ($input->getArguments() as $name => $value) {
            if ($name === 'paths') {
                continue;
            }

            $arguments[] = $value;
        }

        return $arguments;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return string[]
     */
    private function getArgumentsFromInputValue($name, $value)
    {
        $arguments = [];
        switch (gettype($value)) {
            case 'boolean':
                if ($value) {
                    $arguments[] = '--' . $name;
                }

                break;
            case 'integer':
            case 'double':
            case 'string':
                $arguments[] = '--' . $name;
                $arguments[] = $value;
                break;
            case 'array':
                foreach ($value as $valueContent) {
                    $arguments[] = '--' . $name;
                    if (! $valueContent) {
                        continue;
                    }

                    $arguments[] = $valueContent;
                }

                break;
        }

        return $arguments;
    }

    /**
     * @return string
     */
    private function getBehatBin()
    {
        if (! defined('BEHAT_BIN_PATH')) {
            throw new Runtime('constant BEHAT_BIN_PATH is not defined.');
        }

        return BEHAT_BIN_PATH;
    }

    /**
     * @return string
     */
    private function getPHPBin()
    {
        $php = $this->phpFinder->find();

        if (! is_string($php)) {
            throw new Runtime('Unable to find the PHP executable.');
        }

        return $php;
    }
}
