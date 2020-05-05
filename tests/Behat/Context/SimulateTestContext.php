<?php

namespace DMarynicz\Tests\Behat\Context;

use Behat\Behat\Context\Context;
use Exception;

class SimulateTestContext implements Context
{
    /**
     * @Given /^(?:|I )am on pretending to be on (?:|the )homepage$/
     * @When /^(?:|I )am pretending to go to (?:|the )homepage$/
     */
    public function iAmPretendingOnHomepage()
    {
    }

    /**
     * @Given /^(?:|I )am on pretending "(?P<page>[^"]+)"$/
     * @When /^(?:|I )pretend I am going to "(?P<page>[^"]+)"$/
     */
    public function iAmPretendingOnPage()
    {
    }

    /**
     * @param int $seconds
     *
     * @When /^I wait for (\d+) seconds$/
     */
    public function iWaitForSeconds($seconds)
    {
        sleep($seconds);
    }

    /**
     * @Then this test will fail
     */
    public function thenThisTestWillFail()
    {
        throw new Exception('fail');
    }

    /**
     * @Then /^this test will be successful$/
     */
    public function thenThisTestWillBeSuccessful()
    {
    }
}
