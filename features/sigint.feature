@skip
  # Skipped test because although this functionality works from the keyboard. But this test  cannot emulate keyboard.
  # End if even i send the SIGINT signal then signal is not propagated to the parent process.
Feature: Sigint
  As a programmer, I want to able cancel my tests by using SIGINT signal or by simply pressing CTRL+C.
  Scenario: Run successful behat i parralel mode tests and cancel them after 5 seconds.
    Given I start "behat --config tests/fixtures/fail/behat.yml.dist  --parallel 2"
    And I wait for 3 seconds
    And I send a SIGINT signal to behat process
    Then it should fail