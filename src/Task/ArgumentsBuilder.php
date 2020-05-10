<?php

namespace DMarynicz\BehatParallelExtension\Task;

use Symfony\Component\Console\Input\InputInterface;

interface ArgumentsBuilder
{
    /**
     * @param string $path
     *
     * @return string[]
     */
    public function buildArguments(InputInterface $input, $path);
}
