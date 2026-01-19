<?php

namespace DMarynicz\Tests\Behat\Context;

use Behat\Behat\Context\Context;
use Exception;

class SimulateTestContext implements Context
{
    /*
     * Any value you want to multiply the wait time by.
     * Tips: when running tests many times, you can temporarily set this value to 0.0001 to speed up the tests.
     */
    private const WAIT_TIME_MULTIPLIER = 1;

    /**
     * @Given /^(?:|I )am on pretending to be on (?:|the )homepage$/
     * @When /^(?:|I )am pretending to go to (?:|the )homepage$/
     */
    public function iAmPretendingOnHomepage(): void
    {
    }

    /**
     * @Given /^(?:|I )am on pretending "(?P<page>[^"]+)"$/
     * @When /^(?:|I )pretend I am going to "(?P<page>[^"]+)"$/
     */
    public function iAmPretendingOnPage(): void
    {
    }

    /**
     * @param int $seconds
     *
     * @When /^I wait for (\d+) seconds$/
     */
    public function iWaitForSeconds($seconds): void
    {
        usleep((int) ($seconds * 1000 * 1000 * self::WAIT_TIME_MULTIPLIER));
    }

    /**
     * @Then this test will fail
     */
    public function thenThisTestWillFail(): void
    {
        throw new Exception('fail');
    }

    /**
     * @Then /^this test will be successful$/
     */
    public function thenThisTestWillBeSuccessful(): void
    {
    }
}
