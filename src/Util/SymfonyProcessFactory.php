<?php

namespace DMarynicz\BehatParallelExtension\Util;

use Symfony\Component\Process\Process;

final class SymfonyProcessFactory implements ProcessFactory
{
    /**
     * @param array<string>      $command
     * @param string|null        $cwd
     * @param array<string>|null $environment
     * @param null               $input
     * @param float|int|null     $timeout
     *
     * @return Process<string, string>
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
