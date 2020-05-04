<?php

namespace DMarynicz\BehatParallelExtension\Queue;

use Countable;

class Queue implements Countable
{
    const SERVICE_ID = 'parallel_extension.queue';

    /** @var Task[] */
    private $elements = [];

    public function enqueue(Task $task)
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
     * @return Task|null
     */
    public function dequeue()
    {
        return array_shift($this->elements);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->elements);
    }
}
