<?php

namespace DMarynicz\Tests\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use DMarynicz\BehatParallelExtension\Exception\Logic;
use DMarynicz\Tests\Behat\Util\ReadWriteDataToFileWithLocking;
use PHPUnit\Framework\Assert;

class EnvironmentContext implements Context
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
     * @When I create empty json file in :filename
     */
    public function iCreateEmptyJsonFile($filename)
    {
        $path   = $this->getRealPath($filename);
        $handle = new ReadWriteDataToFileWithLocking($path, 'w+b');
        $handle->truncateAndWrite('[]');
    }

    /**
     * @param string $name
     * @param string $filename
     *
     * @When I append the value of the environment variable :name variable to json :filename
     */
    public function iAppendEnvironmentVariableToJson($name, $filename)
    {
        $path   = $this->getRealPath($filename);
        $handle = new ReadWriteDataToFileWithLocking($path);

        $data = $handle->read();

        $array = json_decode($data);
        if (! is_array($array)) {
            throw new Logic('Expected array');
        }

        $array[] = getenv($name);
        $data    = json_encode($array);
        if (! is_array($array)) {
            throw new Logic('Expected array');
        }

        if (! is_string($data)) {
            throw new Logic('Expected string');
        }

        $handle->truncateAndWrite($data);
    }

    /**
     * @param string            $filename
     * @param TableNode<string> $tableNode
     *
     * @When the ordered unique data of the :filename json file should match:
     */
    public function theOrderedUniqueDataOfTheFileShouldMatch($filename, TableNode $tableNode)
    {
        $path   = $this->getRealPath($filename);
        $handle = new ReadWriteDataToFileWithLocking($path);
        $data   = $handle->read();
        $array  = json_decode($data);

        $array = array_unique($array);
        sort($array);

        $actualCount = count($array);
        if (! is_int($actualCount)) {
            throw new Logic('Expected int');
        }

        Assert::assertCount($actualCount, $tableNode->getRows());

        foreach ($tableNode->getRows() as $index => $row) {
            Assert::assertArrayHasKey($index, $array);
            $expectedText = implode($row);
            $actual       = $array[$index];
            Assert::assertEquals($expectedText, $actual);
        }
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    private function getRealPath($filename)
    {
        return $this->getRealFilesPath() . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * @return string
     */
    private function getRealFilesPath()
    {
        $path = $this->filesPath ? $this->filesPath : '';
        $path = realpath($path);
        $path = $path ? $path : '';

        return rtrim($path, DIRECTORY_SEPARATOR);
    }
}
