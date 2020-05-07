Feature: Parallel Rerun
  As a programmer, I want to able rerun failed tests in Parallel mode.
  Scenario: Test behat tests with failed result
    Given I run "behat --config tests/fixtures/fail/behat.yml.dist  -l 20"
    Then it should fail with:
    """
    suite04<DIRECTORY_SEPARATOR>fail.feature:19
    """
    And it should fail with:
    """
    suite01<DIRECTORY_SEPARATOR>successful.feature:22
    """
    And I should see progress bar
    And I run "behat --config tests/fixtures/fail/behat.yml.dist --rerun"
    And it should fail with:
    """
    2 scenarios (2 failed)
    """
    And it should fail with:
    """
    suite01<DIRECTORY_SEPARATOR>successful.feature:22
    """
    And it should fail with:
    """
    suite04<DIRECTORY_SEPARATOR>fail.feature:19
    """
