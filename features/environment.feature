Feature: Environment
  As a programmer, I want to be able add my environments vars to parallel workers.

  Scenario: I create empty json file in test.json
    Given I create empty json file in "test.json"
    Then I run "behat --config tests/fixtures/environments/behat.yml.dist -l 4"
    And the ordered data of the "test.json" json file should match:
    | 0 |
    | 1 |
    | 2 |
    | 3 |

