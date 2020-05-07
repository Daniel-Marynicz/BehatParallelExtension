Feature: Parallel
  As a programmer, I want to able test my tests in parallel mode.
  And if test will fail then i want see my errors.
  Scenario: Test behat tests with successful result and -l option
    Given I run "behat --config tests/fixtures/successful/behat.yml.dist -l 20"
    Then it should pass
    And I should see progress bar
  Scenario: Test behat tests with successful result and --parallel option
    Given I run "behat --config tests/fixtures/successful/behat.yml.dist --parallel 20"
    Then it should pass
    And I should see progress bar
  Scenario: Test behat tests with failed result
    Given I run "behat --config tests/fixtures/fail/behat.yml.dist  -l 20 --no-colors"
    Then it should fail with:
    """
    suite04<DIRECTORY_SEPARATOR>fail.feature:19
    """
    And it should fail with:
    """
    suite01<DIRECTORY_SEPARATOR>successful.feature:22
    """
    And I should see progress bar
