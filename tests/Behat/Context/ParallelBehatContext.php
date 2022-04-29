<?php

namespace DMarynicz\Tests\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use DMarynicz\BehatParallelExtension\Exception\Runtime;
use DMarynicz\BehatParallelExtension\Exception\UnexpectedValue;
use PHPUnit\Framework\Assert;
use RuntimeException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class ParallelBehatContext implements Context
{
    /** @var string */
    private $phpBin;

    /** @var Process<string> */
    private $process;

    /**
     * Prepares test folders in the temporary directory.
     *
     * @BeforeScenario
     */
    public function findAndSetPhpBin()
    {
        $phpFinder = new PhpExecutableFinder();
        $php       = $phpFinder->find();
        if ($php === false) {
            throw new RuntimeException('Unable to find the PHP executable.');
        }

        $this->phpBin = $php;
    }

    /**
     * Runs behat command with provided parameters
     *
     * @param string $argumentsString
     *
     * @When /^I run "behat(?: ((?:\"|[^"])*))?"$/
     */
    public function iRunBehat($argumentsString = '')
    {
        if (! defined('BEHAT_BIN_PATH')) {
            throw new Runtime('constant BEHAT_BIN_PATH is not defined.');
        }

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
     * Starts behat command in non blocking way with provided parameters
     *
     * @param string $argumentsString
     *
     * @When /^I start "behat(?: ((?:\"|[^"])*))?"$/
     */
    public function iStartBehat($argumentsString = '')
    {
        if (! defined('BEHAT_BIN_PATH')) {
            throw new Runtime('constant BEHAT_BIN_PATH is not defined.');
        }

        $cmd = sprintf(
            '%s %s %s',
            $this->phpBin,
            escapeshellarg(BEHAT_BIN_PATH),
            $argumentsString
        );

        $this->processFromShellCommandline($cmd);
        $this->process->start();
    }

    /**
     * @Then /^I send a SIGINT signal to behat process$/
     */
    public function iSendSigintSignalToBehatProcess()
    {
        $this->process->signal(SIGINT);
    }

    /**
     * Checks whether previously ran command passes|fails with provided output.
     *
     * @param string       $success "fail" or "pass"
     * @param PyStringNode $text    PyString text instance
     *
     * @Then /^it should (fail|pass) with:$/
     */
    public function itShouldPassWith($success, PyStringNode $text)
    {
        $this->itShouldFail($success);
        $this->theOutputShouldContain($text);
    }

    /**
     * Checks whether previously ran command failed|passed.
     *
     * @param string $success "fail" or "pass"
     *
     * @Then /^it should (fail|pass)$/
     */
    public function itShouldFail($success)
    {
        if ($success === 'fail') {
            if ($this->getExitCode() === 0) {
                echo 'Actual output:' . PHP_EOL . PHP_EOL . $this->getOutput();
            }

            Assert::assertNotEquals(0, $this->getExitCode());
        } else {
            if ($this->getExitCode() !== 0) {
                echo 'Actual output:' . PHP_EOL . PHP_EOL . $this->getOutput();
            }

            Assert::assertEquals(0, $this->getExitCode());
        }
    }

    /**
     * Checks whether last command output contains provided string.
     *
     * @param PyStringNode $text PyString text instance
     *
     * @Then the output should contain:
     */
    public function theOutputShouldContain(PyStringNode $text)
    {
        Assert::assertStringContainsString((string) $text, $this->getOutput());
    }

    /**
     * @Then /^I should see progress bar$/
     */
    public function iShouldSeeProgressBar()
    {
        Assert::assertStringContainsString('[============================] 100%', $this->getOutput());
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
            // @phpstan-ignore-next-line
            $this->process = new Process(null);
            // @phpstan-ignore-next-line
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

    /**
     * @return string
     */
    private function getOutput()
    {
        $output = $this->process->getErrorOutput() . $this->process->getOutput();

        // Normalize the line endings and directory separators in the output
        if (PHP_EOL !== "\n") {
            $output = str_replace(PHP_EOL, "\n", $output);
        }

        // Remove location of the project
        $output = str_replace(realpath(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR, '', $output);

        $output = preg_replace('/ +$/m', '', $output);
        if (! is_string($output)) {
            throw new UnexpectedValue('Expected string');
        }

        return trim($output);
    }
}
