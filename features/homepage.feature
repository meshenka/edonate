Feature: Test homepage

  @reset-schema
  @fixtures
  Scenario: Init data (create schema and load fixtures)
    Given I am on the homepage


  Scenario: Test as an anonymous user that I can access the homepage
    Given I go to the homepage
    Then the response status code should be 200
    And I should see "Association de test"