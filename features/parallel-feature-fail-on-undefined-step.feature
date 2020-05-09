Feature: Fail On Undefined step
  As a programmer, I want my tests to fail if there are any undefined steps
  Scenario: Test behat tests with undefined step. Test will fail.
    Given I run "behat --config tests/fixtures/undefined-steps/behat.yml.dist --parallel-feature 20"
    Then it should fail with:
    """
    Tests has undefined steps!
    """
    And I should see progress bar
