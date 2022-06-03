<?php

namespace DMarynicz\Tests\Behat\Util;

use DMarynicz\BehatParallelExtension\Exception\Logic;

class ReadWriteDataToFileWithLocking
{
    /** @var string */
    private $path;
    /** @var resource */
    private $handle;

    /**
     * @param string $path
     * @param string $mode
     */
    public function __construct($path, $mode = 'r+b')
    {
        $this->path = $path;
        $handle     = fopen($path, $mode);
        if (! $handle) {
            throw new Logic("Can't open file");
        }

        $this->handle = $handle;
        if (! flock($this->handle, LOCK_EX)) {
            throw new Logic("Can't exclusive local file");
        }
    }

    public function read(): string
    {
        rewind($this->handle);
        $fileSize = filesize($this->path);
        if ($fileSize === false) {
            throw new Logic("Can't get file size");
        }

        $data = fread($this->handle, $fileSize);
        if (! is_string($data)) {
            throw new Logic("Can't read data");
        }

        return $data;
    }

    /**
     * @param string $data
     */
    public function truncateAndWrite($data): void
    {
        ftruncate($this->handle, 0);
        rewind($this->handle);
        fwrite($this->handle, $data);
    }

    public function __destruct()
    {
        fflush($this->handle);
        flock($this->handle, LOCK_UN);
        fclose($this->handle);
    }
}
