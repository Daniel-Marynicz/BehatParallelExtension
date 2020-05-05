Feature: test

  Background:
    Given I am on pretending to be on the homepage
    And I pretend I am going to "asdad"
  Scenario:
    Given I wait for 1 seconds
    Then this test will be successful
  Scenario: test 2
    Given I wait for 3 seconds
    Then this test will be successful
  Scenario Outline:
    Given I wait for <seconds> seconds
    Then this test will be successful
    Examples:
    | seconds |
    | 1       |
    | 2       |
  Scenario: test 4
    Given I wait for 3 seconds
    Then this test will be successful
  Scenario: test 5
    Given I wait for 3 seconds
    Then this test will fail



