<?php

namespace DMarynicz\BehatParallelExtension\Task;

use Symfony\Component\Console\Input\InputInterface;

interface ArgumentsBuilder
{
    /**
     * @param string[] $paths
     *
     * @return string[]
     */
    public function buildArguments(InputInterface $input, array $paths): array;
}
