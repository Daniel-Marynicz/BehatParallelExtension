<?php

namespace DMarynicz\Tests\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
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
        Assert::assertIsArray($array);
        $array[] = getenv($name);
        $data    = json_encode($array);
        Assert::assertIsString($data);

        $handle->truncateAndWrite($data);
    }

    /**
     * @param string            $filename
     * @param TableNode<string> $tableNode
     *
     * @When the ordered data of the :filename json file should match:
     */
    public function theOrderedDataOfTheFileShouldMatch($filename, TableNode $tableNode)
    {
        $path   = $this->getRealPath($filename);
        $handle = new ReadWriteDataToFileWithLocking($path);
        $data   = $handle->read();
        $array  = json_decode($data);

        $actualCount = count($array);

        Assert::assertIsInt($actualCount);
        Assert::assertCount($actualCount, $tableNode->getRows());

        sort($array);
        foreach ($tableNode as $index => $expectedText) {
            Assert::assertArrayHasKey($index, $array);
            Assert::assertEquals($expectedText, $array[$index]);
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
