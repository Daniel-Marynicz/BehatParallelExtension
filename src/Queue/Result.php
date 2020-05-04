<?php

namespace DMarynicz\BehatParallelExtension\Queue;

class Result
{
    /** @var Task[] */
    private $successful = [];

    /** @var Task[] */
    private $failed = [];

    public function addSuccessful(Task $task)
    {
        $this->successful[] = $task;
    }

    public function addFailed(Task $task)
    {
        $this->failed[] = $task;
    }

    /**
     * @return Task[]
     */
    public function getSuccessful()
    {
        return $this->successful;
    }

    /**
     * @return Task[]
     */
    public function getFailed()
    {
        return $this->failed;
    }
}
