<?php

namespace DMarynicz\BehatParallelExtension\Task;

use DMarynicz\BehatParallelExtension\Exception\Runtime;
use ReflectionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Process\PhpExecutableFinder;

final class TaskArgumentsBuilder implements ArgumentsBuilder
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

        $argsFromInputValue = [];
        foreach ($input->getOptions() as $name => $value) {
            if (in_array($name, ['parallel', 'parallel-feature'])) {
                continue;
            }

            $argsFromInputValue[] = $this->getArgumentsFromInputValue($name, $value);
        }

        return array_merge($arguments, ...$argsFromInputValue);
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
        switch (gettype($value)) {
            case 'boolean':
                return $this->getBoolArgument($name, $value);

            case 'array':
                return $this->getArrayArguments($name, $value);

            case 'integer':
            case 'double':
            case 'string':
                return $this->getStringArguments($name, (string) $value);

            default:
                return [];
        }
    }

    /**
     * @param string $name
     * @param bool   $value
     *
     * @return array|string[]
     */
    private function getBoolArgument($name, $value)
    {
        if ($value) {
            return ['--' . $name];
        }

        return [];
    }

    /**
     * @param string         $name
     * @param array|string[] $value
     *
     * @return array|string[]
     */
    private function getArrayArguments($name, $value)
    {
        $arguments = [];
        foreach ($value as $valueContent) {
            $arguments[] = '--' . $name;
            if (! $valueContent) {
                continue;
            }

            $arguments[] = $valueContent;
        }

        return $arguments;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return array|string[]
     */
    private function getStringArguments($name, $value)
    {
        return [
            '--' . $name,
            $value,
        ];
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
