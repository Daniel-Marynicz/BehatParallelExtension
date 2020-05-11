<?php

namespace DMarynicz\BehatParallelExtension\Util;

use Symfony\Component\Process\Process;

class SymfonyProcessFactory
{
    /**
     * @param array<string>      $command
     * @param string|null        $cwd
     * @param array<string>|null $environment
     * @param null               $input
     * @param float|int|null     $timeout
     *
     * @return Process<string>
     */
    public function createNewProcess(
        array $command,
        $cwd = null,
        array $environment = null,
        $input = null,
        $timeout = 60
    ) {
        return new Process($command, $cwd, $environment, $input, $timeout);
    }
}
