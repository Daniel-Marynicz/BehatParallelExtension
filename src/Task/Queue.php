<?php

namespace DMarynicz\BehatParallelExtension\Task;

use Countable;

interface Queue extends Countable
{
    /**
     * Dispatch new task in to queue
     */
    public function dispatch(TaskEntity $task): void;

    /**
     * @return bool
     */
    public function isEmpty();

    /**
     * Shift the next element off of the queue.
     *
     * @return TaskEntity|null
     */
    public function shift();

    /**
     * Count elements of an queue
     */
    public function count(): int;
}
