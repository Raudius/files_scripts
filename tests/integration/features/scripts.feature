Feature: scripts
  Scenario: Run scripts
    Given "admin" deletes all scripts
    And "admin" deletes all tags
    Then script "test_files.lua" is run with "no" output
    And script "test_nextcloud.lua" is run with "no" output
    And script "test_util.lua" is run with "no" output

    Then script "test_messages.lua" is run with "test_messages_out.json" output
    Then script "test_abort.lua" is run with "test_abort_out.json" output
    Then script "test_abort_with_messages.lua" is run with "test_abort_with_messages_out.json" output
