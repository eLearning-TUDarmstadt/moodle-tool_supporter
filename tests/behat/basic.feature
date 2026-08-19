@tool @tool_supporter
Feature: Use the supporter tool
  In order to support Moodle users
  As an administrator
  I need to be able to quickly list and find courses and users

  @javascript
  Scenario: Plugin tool_supporter appears in the list of installed additional plugins
    Given I log in as "admin"
    When I navigate to "Plugins > Plugins overview" in site administration
    And I follow "Additional plugins"
    Then I should see "Supporter"
    And I should see "tool_supporter"

  @javascript
  Scenario: View courses and users in the supporter tool
    Given the following "categories" exist:
      | name                   | idnumber | category |
      | Science and technology | scitech  |          |
      | Physics                | st-phys  | scitech  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Maths    | math102   | st-phys  |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | John      | Doe      | john.doe@example.com |
    When I log in as "admin"
    And I navigate to "Supporter" in site administration
    And I follow "Supporter"
    And I wait until the page is ready
    Then I should see "Maths"
    And I should see "John"
    And I should see "Doe"

  @javascript
  Scenario: View courses with special characters in the supporter tool
    Given the following "categories" exist:
      | name                         | idnumber | category |
      | Science and technology < > & | scitech  |          |
      | Physics < > &                | st-phys  | scitech  |
    And the following "courses" exist:
      | fullname    | shortname | category |
      | Maths < > & | math102   | st-phys  |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | John      | Doe      | john.doe@example.com |
    When I log in as "admin"
    And I navigate to "Supporter" in site administration
    And I follow "Supporter"
    And I wait until the page is ready
    When I click on "Maths" "table_row" in the "#courseTable" "css_element"
    Then I should see "Maths < > &"
    But I should not see "&lt; &gt; &amp;"
