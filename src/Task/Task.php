<?php

namespace DMarynicz\BehatParallelExtension\Task;

use Behat\Testwork\Suite\Suite;

final class Task implements TaskEntity
{
    /** @var Suite */
    private $suite;

    /** @var TaskUnit[] */
    private $units;

    /** @var string[] */
    private $command;

    /**
     * @param TaskUnit[] $units
     * @param string[]   $command
     */
    public function __construct(
        Suite $suite,
        array $units,
        array $command = []
    ) {
        $this->suite   = $suite;
        $this->units   = $units;
        $this->command = $command;
    }

    /**
     * @return TaskUnit[]
     */
    public function getUnits(): array
    {
        return $this->units;
    }

    /**
     * @return Suite
     */
    public function getSuite()
    {
        return $this->suite;
    }

    /**
     * @return string[]
     */
    public function getPaths(): array
    {
        return array_map(static function (TaskUnit $unit) {
            return $unit->getPath();
        }, $this->units);
    }

    /**
     * @return string[]
     */
    public function getCommand()
    {
        return $this->command;
    }
}
