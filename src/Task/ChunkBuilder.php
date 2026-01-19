<?php

namespace DMarynicz\BehatParallelExtension\Task;

/**
 * Utility to group items into chunks and create tasks from them.
 */
final class ChunkBuilder
{
    /**
     * Groups items into chunks and applies a creator function to each chunk.
     *
     * @param iterable<mixed> $items       Items to group
     * @param int             $chunkSize   Maximum number of items per chunk
     * @param callable        $taskCreator Function to create a task from a chunk
     *
     * @return TaskEntity[] List of created tasks
     */
    public static function buildChunks($items, $chunkSize, $taskCreator): array
    {
        $tasks = [];
        $chunk = [];
        foreach ($items as $item) {
            $chunk[] = $item;
            if (count($chunk) < $chunkSize) {
                continue;
            }

            $tasks[] = $taskCreator($chunk);
            $chunk   = [];
        }

        if ($chunk) {
            $tasks[] = $taskCreator($chunk);
        }

        return $tasks;
    }
}
