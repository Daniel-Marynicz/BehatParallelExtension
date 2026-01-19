<?php

namespace DMarynicz\BehatParallelExtension\Task;

use Behat\Testwork\Suite\Suite;

interface TaskEntity
{
    /**
     * @return Suite
     */
    public function getSuite();

    /**
     * @return string[]
     */
    public function getPaths();

    /**
     * @return string[]
     */
    public function getCommand();

    /**
     * @return TaskUnit[]
     */
    public function getUnits(): array;
}
