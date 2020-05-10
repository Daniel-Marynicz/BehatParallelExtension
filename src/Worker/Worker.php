<?php

namespace DMarynicz\BehatParallelExtension\Worker;

interface Worker
{
    /**
     * Starts Worker
     */
    public function start();

    public function wait();

    /**
     * There is a Running Process?
     *
     * @return bool
     */
    public function isRunning();

    /**
     * Worker is started?
     *
     * @return bool
     */
    public function isStarted();

    /**
     * Stops worker
     */
    public function stop();

    /**
     * Returns current worker environment
     *
     * @return string[]
     */
    public function getEnvironment();

    /**
     * Sets worker environment
     *
     * @param string[] $env
     *
     * @return Worker
     */
    public function setEnvironment($env);

    /**
     * Return's worker id
     *
     * @return int
     */
    public function getWorkerId();
}
