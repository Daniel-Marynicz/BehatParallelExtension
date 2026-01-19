<?php

namespace DMarynicz\BehatParallelExtension\Worker;

interface Worker
{
    /**
     * Starts Worker
     */
    public function start(): void;

    public function wait(): void;

    /**
     * There is a Running Process?
     */
    public function isRunning(): bool;

    /**
     * Worker is started?
     */
    public function isStarted(): bool;

    /**
     * Stops worker
     */
    public function stop(): void;

    /**
     * Returns current worker environment
     *
     * @return string[]
     */
    public function getEnvironment(): array;

    /**
     * Sets worker environment
     *
     * @param string[] $env
     */
    public function setEnvironment(array $env): void;

    /**
     * Return's worker id
     */
    public function getWorkerId(): int;
}
