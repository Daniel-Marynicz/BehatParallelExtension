<?php

namespace DMarynicz\BehatParallelExtension\Task;

class TaskQueue implements Queue
{
    /** @var TaskEntity[] */
    private $elements = [];

    public function dispatch(TaskEntity $task): void
    {
        $this->elements[] = $task;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->elements);
    }

    /**
     * Shift the next element off of the queue.
     *
     * @return TaskEntity|null
     */
    public function shift()
    {
        return array_shift($this->elements);
    }

    /**
     * Count elements of an queue
     */
    public function count(): int
    {
        return count($this->elements);
    }
}
