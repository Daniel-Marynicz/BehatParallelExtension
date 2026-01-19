@skip_without_multiple_paths
Feature: Parallel chunk size parameter
  As a programmer, I want to able to group my tests in chunks to reduce overhead.

  Scenario: Test behat tests with --parallel-chunk-size and --parallel-feature option
    Given I delete file "tests/fixtures/chunked/chunk_test.json"
    When I run "behat --config tests/fixtures/chunked/behat.yml.dist --parallel-feature 20 --parallel-chunk-size 2"
    Then it should pass
    And I should see progress bar
    And the output should contain:
    """
    Starting parallel tests with 20 workers and 2 tests per worker
    """
    # suite01 has 2 features -> should be 1 chunk containing both
    # suite02 has 1 feature -> should be 1 chunk
    # Total 2 commands expected
    And I should have 2 behat commands in "tests/fixtures/chunked/chunk_test.json"
    Then behat commands in "tests/fixtures/chunked/chunk_test.json" should match:
      """
      {BEHAT_BIN} --no-interaction --fail-on-undefined-step --config tests/fixtures/chunked/behat.yml.dist tests/fixtures/chunked/suite01/f1.feature tests/fixtures/chunked/suite01/f2.feature
      {BEHAT_BIN} --no-interaction --fail-on-undefined-step --config tests/fixtures/chunked/behat.yml.dist tests/fixtures/chunked/suite02/f3.feature
      """

  Scenario: Test behat tests with --parallel-chunk-size and --parallel option
    Given I delete file "tests/fixtures/chunked/chunk_test.json"
    When I run "behat --config tests/fixtures/chunked/behat.yml.dist --parallel 20 --parallel-chunk-size 2"
    Then it should pass
    And I should see progress bar
    And the output should contain:
    """
    Starting parallel tests with 20 workers and 2 tests per worker
    """
    # suite01: f1 (1 scenario), f2 (1 scenario) -> 2 scenarios total -> 1 chunk
    # suite02: f3 (2 scenarios) -> 2 scenarios -> 1 chunk
    # Total 2 commands expected
    And I should have 2 behat commands in "tests/fixtures/chunked/chunk_test.json"
    Then behat commands in "tests/fixtures/chunked/chunk_test.json" should match:
      """
      {BEHAT_BIN} --no-interaction --fail-on-undefined-step --config tests/fixtures/chunked/behat.yml.dist tests/fixtures/chunked/suite01/f1.feature:2 tests/fixtures/chunked/suite01/f2.feature:2
      {BEHAT_BIN} --no-interaction --fail-on-undefined-step --config tests/fixtures/chunked/behat.yml.dist tests/fixtures/chunked/suite02/f3.feature:2 tests/fixtures/chunked/suite02/f3.feature:4
      """

  Scenario: Test behat tests with --parallel-chunk-size 1 (default)
    Given I delete file "tests/fixtures/chunked/chunk_test.json"
    When I run "behat --config tests/fixtures/chunked/behat.yml.dist --parallel 20"
    Then it should pass
    And I should see progress bar
    And the output should contain:
    """
    Starting parallel tests with 20 workers
    """
    # 3 features total, 4 scenarios total
    # suite01: f1 (1 scenario), f2 (1 scenario)
    # suite02: f3 (2 scenarios)
    # Total 4 scenarios -> 4 commands
    And I should have 4 behat commands in "tests/fixtures/chunked/chunk_test.json"
    Then behat commands in "tests/fixtures/chunked/chunk_test.json" should match:
      """
      {BEHAT_BIN} --no-interaction --fail-on-undefined-step --config tests/fixtures/chunked/behat.yml.dist tests/fixtures/chunked/suite01/f1.feature:2
      {BEHAT_BIN} --no-interaction --fail-on-undefined-step --config tests/fixtures/chunked/behat.yml.dist tests/fixtures/chunked/suite01/f2.feature:2
      {BEHAT_BIN} --no-interaction --fail-on-undefined-step --config tests/fixtures/chunked/behat.yml.dist tests/fixtures/chunked/suite02/f3.feature:2
      {BEHAT_BIN} --no-interaction --fail-on-undefined-step --config tests/fixtures/chunked/behat.yml.dist tests/fixtures/chunked/suite02/f3.feature:4
      """

  Scenario: Test behat tests with --rerun and --parallel-chunk-size 2
    Given I delete file "tests/fixtures/chunked/chunk_test.json"
    # Rerun should still execute chunked tasks when no previous failures exist.
    When I run "behat --config tests/fixtures/chunked/behat.yml.dist --parallel 2 --parallel-chunk-size 2 --rerun"
    Then it should pass
    And I should see progress bar
    And the output should contain:
      """
      Starting parallel tests with 2 workers and 2 tests per worker
      """
    And I should have 2 behat commands in "tests/fixtures/chunked/chunk_test.json"
    Then behat commands in "tests/fixtures/chunked/chunk_test.json" should match:
      """
      {BEHAT_BIN} --no-interaction --fail-on-undefined-step --rerun --config tests/fixtures/chunked/behat.yml.dist tests/fixtures/chunked/suite01/f1.feature:2 tests/fixtures/chunked/suite01/f2.feature:2
      {BEHAT_BIN} --no-interaction --fail-on-undefined-step --rerun --config tests/fixtures/chunked/behat.yml.dist tests/fixtures/chunked/suite02/f3.feature:2 tests/fixtures/chunked/suite02/f3.feature:4
      """

  Scenario: Test behat tests with --rerun and one failing job with --parallel option
    Given I delete file "tests/fixtures/chunked_rerun/chunk_test.json"
    # First run executes at least two chunks from suite01.
    And I run "behat --config tests/fixtures/chunked_rerun/behat.yml.dist --parallel 2 --parallel-chunk-size 2"
    Then it should fail
    And I should have 2 behat commands in "tests/fixtures/chunked_rerun/chunk_test.json"
    And behat commands in "tests/fixtures/chunked_rerun/chunk_test.json" should match:
      """
      {BEHAT_BIN} --no-interaction --fail-on-undefined-step --config tests/fixtures/chunked_rerun/behat.yml.dist tests/fixtures/chunked_rerun/suite01/f1.feature:2 tests/fixtures/chunked_rerun/suite01/f2.feature:2
      {BEHAT_BIN} --no-interaction --fail-on-undefined-step --config tests/fixtures/chunked_rerun/behat.yml.dist tests/fixtures/chunked_rerun/suite01/f3.feature:2
      """
    And I delete file "tests/fixtures/chunked_rerun/chunk_test.json"
    # Rerun only the previously failed command from suite01.
    When I run "behat --config tests/fixtures/chunked_rerun/behat.yml.dist --parallel 2 --parallel-chunk-size 2 --rerun"
    Then it should fail
    And I should see progress bar
    And the output should contain:
      """
      Starting parallel tests with 2 workers and 2 tests per worker
      """
    And I should have 1 behat commands in "tests/fixtures/chunked_rerun/chunk_test.json"
    And behat commands in "tests/fixtures/chunked_rerun/chunk_test.json" should match:
      """
      {BEHAT_BIN} --no-interaction --fail-on-undefined-step --rerun --config tests/fixtures/chunked_rerun/behat.yml.dist tests/fixtures/chunked_rerun/suite01/f1.feature:2 tests/fixtures/chunked_rerun/suite01/f2.feature:2
      """

  Scenario: Test behat tests with --rerun and one failing job with --parallel-feature option
    Given I delete file "tests/fixtures/chunked_rerun/chunk_test.json"
    # First run executes at least two chunks from suite01.
    And I run "behat --config tests/fixtures/chunked_rerun/behat.yml.dist --parallel-feature 2 --parallel-chunk-size 2"
    Then it should fail
    And I should have 2 behat commands in "tests/fixtures/chunked_rerun/chunk_test.json"
    And behat commands in "tests/fixtures/chunked_rerun/chunk_test.json" should match:
      """
      {BEHAT_BIN} --no-interaction --fail-on-undefined-step --config tests/fixtures/chunked_rerun/behat.yml.dist tests/fixtures/chunked_rerun/suite01/f1.feature tests/fixtures/chunked_rerun/suite01/f2.feature
      {BEHAT_BIN} --no-interaction --fail-on-undefined-step --config tests/fixtures/chunked_rerun/behat.yml.dist tests/fixtures/chunked_rerun/suite01/f3.feature
      """
    And I delete file "tests/fixtures/chunked_rerun/chunk_test.json"
    # Rerun only the previously failed command from suite01.
    When I run "behat --config tests/fixtures/chunked_rerun/behat.yml.dist --parallel-feature 2 --parallel-chunk-size 2 --rerun"
    Then it should fail
    And I should see progress bar
    And the output should contain:
      """
      Starting parallel tests with 2 workers and 2 tests per worker
      """
    And I should have 1 behat commands in "tests/fixtures/chunked_rerun/chunk_test.json"
    And behat commands in "tests/fixtures/chunked_rerun/chunk_test.json" should match:
      """
      {BEHAT_BIN} --no-interaction --fail-on-undefined-step --rerun --config tests/fixtures/chunked_rerun/behat.yml.dist tests/fixtures/chunked_rerun/suite01/f1.feature tests/fixtures/chunked_rerun/suite01/f2.feature
      """
