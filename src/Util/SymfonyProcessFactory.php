<?php

namespace DMarynicz\BehatParallelExtension\Util;

use Symfony\Component\Process\Process;

final class SymfonyProcessFactory implements ProcessFactory
{
    /**
     * {@inheritdoc}
     */
    public function createNewProcess(
        $command,
        $cwd = null,
        $environment = null,
        $input = null,
        $timeout = 60
    ) {
        return new Process($command, $cwd, $environment, $input, $timeout);
    }
}
