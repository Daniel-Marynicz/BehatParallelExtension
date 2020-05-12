<?php

namespace DMarynicz\BehatParallelExtension\Worker;

interface Poll
{
    /**
     * Sets max workers
     *
     * @param int $maxWorkers
     */
    public function setMaxWorkers($maxWorkers);

    /**
     * Starts poll
     *
     * @return $this
     */
    public function start();

    /**
     * Wait for all workers to finish processing
     */
    public function wait();

    /**
     * Poll has started workers?
     *
     * @return bool
     */
    public function hasStartedWorkers();

    /**
     * Returns total workers
     *
     * @return int
     */
    public function getTotalWorkers();

    /**
     * Poll is started?
     *
     * @return bool
     */
    public function isStarted();

    /**
     * Stops poll
     */
    public function stop();
}
