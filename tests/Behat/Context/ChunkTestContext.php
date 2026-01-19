<?php

namespace DMarynicz\Tests\Behat\Context;

use Behat\Behat\Context\Context;
use DMarynicz\BehatParallelExtension\Exception\Logic;
use DMarynicz\Tests\Behat\Util\ReadWriteDataToFileWithLocking;

class ChunkTestContext implements Context
{
    /** @var string|null */
    private $filesPath;

    /**
     * @param string|null $filesPath
     */
    public function __construct($filesPath = null)
    {
        $this->filesPath = $filesPath;
    }

    /**
     * @param string $filename
     *
     * @Given I log behat command to :filename
     */
    public function iLogBehatCommandTo($filename): void
    {
        $path = $this->getRealPath($filename);
        if (! file_exists($path)) {
            file_put_contents($path, '[]');
        }

        $handle = new ReadWriteDataToFileWithLocking($path);

        $data = $handle->read();

        $array = json_decode($data, true);
        if (! is_array($array)) {
            $array = [];
        }

        $argv = $_SERVER['argv'];
        $root = realpath(__DIR__ . '/../../../') . DIRECTORY_SEPARATOR;
        foreach ($argv as &$arg) {
            if (! is_string($arg) || strpos($arg, $root) !== 0) {
                continue;
            }

            $arg = substr($arg, strlen($root));
        }

        if (
            isset($argv[0])
            && is_string($argv[0])
            && strpos($argv[0], 'php') !== false
            && count($argv) > 1
            && isset($argv[1])
            && is_string($argv[1])
            && strpos($argv[1], 'behat') !== false
        ) {
            array_shift($argv);
        }

        $command = implode(' ', $argv);
        if (! in_array($command, $array)) {
            $array[] = $command;
        }

        $data = json_encode($array);

        if (! is_string($data)) {
            throw new Logic('Expected string');
        }

        $handle->truncateAndWrite($data);
    }

    /**
     * @param string $filename
     */
    private function getRealPath($filename): string
    {
        return $this->getRealFilesPath() . DIRECTORY_SEPARATOR . $filename;
    }

    private function getRealFilesPath(): string
    {
        $path = $this->filesPath ?: '';
        $path = (string) realpath($path);

        return rtrim($path, DIRECTORY_SEPARATOR);
    }
}
