<?php

namespace DMarynicz\BehatParallelExtension\Task;

use Symfony\Component\Console\Input\InputInterface;

interface TaskFactory
{
    /**
     * @param string|null $path
     *
     * @return Task[]
     */
    public function createTasks(InputInterface $input, $path);
}
