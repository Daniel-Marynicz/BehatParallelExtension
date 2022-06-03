<?php

namespace DMarynicz\BehatParallelExtension\Worker;

interface Poll
{
    /**
     * Sets max workers
     *
     * @param int $maxWorkers
     */
    public function setMaxWorkers($maxWorkers): void;

    /**
     * Starts poll
     *
     * @return $this
     */
    public function start(): Poll;

    /**
     * Wait for all workers to finish processing
     */
    public function wait(): void;

    /**
     * Poll has started workers?
     */
    public function hasStartedWorkers(): bool;

    /**
     * Returns total workers
     */
    public function getTotalWorkers(): int;

    /**
     * Poll is started?
     */
    public function isStarted(): bool;

    /**
     * Stops poll
     */
    public function stop(): void;
}
