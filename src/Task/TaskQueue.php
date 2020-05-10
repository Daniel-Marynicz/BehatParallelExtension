<?php

namespace DMarynicz\BehatParallelExtension\Task;

class TaskQueue implements Queue
{
    const SERVICE_ID = 'parallel_extension.queue';

    /** @var TaskEntity[] */
    private $elements = [];

    public function dispatch(TaskEntity $task)
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
     *
     * @return int
     */
    public function count()
    {
        return count($this->elements);
    }
}
