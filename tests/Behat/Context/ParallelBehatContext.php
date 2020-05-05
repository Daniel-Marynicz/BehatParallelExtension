<?php

namespace DMarynicz\Tests\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use PHPUnit\Framework\Assert;
use RuntimeException;

class ParallelBehatContext implements Context
{
    /**
     * @var string
     */
    private $phpBin;

    /**
     * @var Process
     */
    private $process;

    /**
     * Prepares test folders in the temporary directory.
     *
     * @BeforeScenario
     */
    public function prepareTestFolders()
    {
        $phpFinder = new PhpExecutableFinder();
        $php = $phpFinder->find();
        if (false === $php) {
            throw new RuntimeException('Unable to find the PHP executable.');
        }
        $this->phpBin = $php;
    }


    /**
     * Runs behat command with provided parameters
     *
     * @When /^I run "behat(?: ((?:\"|[^"])*))?"$/
     *
     * @param string $argumentsString
     */
    public function iRunBehat($argumentsString = '')
    {
        $cmd = sprintf(
            '%s %s %s',
            $this->phpBin,
            escapeshellarg(BEHAT_BIN_PATH),
                $argumentsString
        );

        $this->processFromShellCommandline($cmd);
        $this->process->run();
    }

    /**
     * Checks whether previously ran command passes|fails with provided output.
     *
     * @Then /^it should (fail|pass) with:$/
     *
     * @param string       $success "fail" or "pass"
     * @param PyStringNode $text    PyString text instance
     */
    public function itShouldPassWith($success, PyStringNode $text)
    {
        $this->itShouldFail($success);
        $this->theOutputShouldContain($text);
    }

    /**
     * Checks whether previously ran command failed|passed.
     *
     * @Then /^it should (fail|pass)$/
     *
     * @param string $success "fail" or "pass"
     */
    public function itShouldFail($success)
    {
        if ('fail' === $success) {
            if (0 === $this->getExitCode()) {
                echo 'Actual output:' . PHP_EOL . PHP_EOL . $this->getOutput();
            }

            Assert::assertNotEquals(0, $this->getExitCode());
        } else {
            if (0 !== $this->getExitCode()) {
                echo 'Actual output:' . PHP_EOL . PHP_EOL . $this->getOutput();
            }

            Assert::assertEquals(0, $this->getExitCode());
        }
    }

    /**
     * Checks whether last command output contains provided string.
     *
     * @Then the output should contain:
     *
     * @param PyStringNode $text PyString text instance
     */
    public function theOutputShouldContain(PyStringNode $text)
    {
        Assert::assertContains((string) $text, $this->getOutput());
    }

    /**
     * @Then /^I should see progress bar$/
     */
    public function iShouldSeeProgressBar()
    {
        Assert::assertContains('[============================] 100%', $this->getOutput());
    }

    /**
     * @Then /^print last output$/
     */
    public function thenPrintLastOutput()
    {
        echo $this->getOutput();
    }

    /**
     * @param string $cmd
     */
    private function processFromShellCommandline($cmd)
    {
        if (method_exists('\\Symfony\\Component\\Process\\Process', 'fromShellCommandline')) {
            $this->process = Process::fromShellCommandline($cmd);
        } else {
            // BC layer for symfony/process 4.1 and older
            $this->process = new Process(null);
            $this->process->setCommandLine($cmd);
        }
    }

    /**
     * @return int|null
     */
    private function getExitCode()
    {
        return $this->process->getExitCode();
    }

    private function getOutput()
    {
        $output = $this->process->getErrorOutput() . $this->process->getOutput();

        // Normalize the line endings and directory separators in the output
        if ("\n" !== PHP_EOL) {
            $output = str_replace(PHP_EOL, "\n", $output);
        }

        // Remove location of the project
        $output = str_replace(realpath(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR, '', $output);

        return trim(preg_replace("/ +$/m", '', $output));
    }
}

