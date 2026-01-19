Feature: Environment
  As a programmer, I want to be able add my environments vars to parallel workers.

  Scenario: I have only configured 4 environments for my poll and only should it start 4 Workers
    Given I create empty json file in "test.json"
    And  I run "behat --config tests/fixtures/environments/behat.yml.dist -l 8"
    Then it should pass
    And the output should contain:
    """
    Starting parallel tests with 8 workers
    """
    And the output should contain:
    """
    Started poll with only 4 workers
    """
    And the ordered unique data of the "test.json" json file should match:
    | 0 |
    | 1 |
    | 2 |
    | 3 |


