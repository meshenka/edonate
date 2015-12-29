Feature: Test admin users section

  @reset-schema
  @fixtures
  Scenario: Init data (create schema and load fixtures)

  Scenario Outline: Test that some profiles can access the back office section
    Given I am authenticated as "<account>" on firewall "secured"
    When I go to "/secured"
    Then the response status code should be 200
    And I should see "Tableau de bord"

    Examples:
      | account     |
      | root        |
      | admin       |
      | cms         |
      | affectation |

  Scenario Outline: Test that some profiles cannot access the layout section
    Given I am authenticated as "<account>" on firewall "secured"
    When I go to "/secured/cms/layouts"
    Then the response status code should be 403

    Examples:
      | account         |
      | user         |
      | affectation  |

  Scenario Outline: Test that some profiles cannot access the users section
    Given I am authenticated as "<account>" on firewall "secured"
    When I go to "/secured/users"
    Then the response status code should be 403

    Examples:
      | account         |
      | cms          |
      | user         |
      | affectation  |
