<?php


namespace DMarynicz\BehatParallelExtension\Event;


class WorkerDestroyed extends Worker
{
    const WORKER_DESTROYED = 'parallel_extension.worker_destroyed';
}