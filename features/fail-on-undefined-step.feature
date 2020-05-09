Feature: Fail On Undefined step
  As a programmer, I want my tests to fail if there are any undefined steps
  Scenario: Test behat tests with undefined step. Test will fail.
    Given I run "behat --config tests/fixtures/undefined-steps/behat.yml.dist  -l 20"
    Then it should fail with:
    """
    Tests has undefined steps!
    """
    And I should see progress bar
  Scenario: If I run behat with option --fail-on-undefined-step and test will have undefined step then test will fail.
    Given I run "behat --no-interaction --fail-on-undefined-step --config tests/fixtures/undefined-steps/behat.yml.dist tests/fixtures/undefined-steps/suite02/undefined.feature"
    Then it should fail with:
    """
    Tests has undefined steps!
    """
  Scenario: If I run behat without option --fail-on-undefined-step and test will have undefined step then test will pass.
    Given I run "behat --no-interaction  --config tests/fixtures/undefined-steps/behat.yml.dist tests/fixtures/undefined-steps/suite02/undefined.feature"
    Then it should pass
