Feature: Test admin authentication

  @reset-schema
  @fixtures
  Scenario: Init data (create schema and load fixtures)

  Scenario: Test as an anonymous user that I must log in to access admin
    Given I go to "/secured"
    Then the response status code should be 200
    And I should be on "/secured/login"
    And I should see "Login"
    And I should see "Mot de passe"

  Scenario: Test to log in with bad credentials
    Given I go to "/secured"
    When I fill in the following:
      | username | john |
      | password | doe |
    And I press "_submit"
    Then the response status code should be 200
    And I should be on "/secured/login"
    And I should see "Bad credentials."

  Scenario: Test to log in with correct credentials
    Given I go to "/secured"
    When I fill in the following:
      | username | root |
      | password | root          |
    And I press "_submit"
    Then the response status code should be 200
    And I should be on "/secured/"
    And I should see "Tableau de bord"
    And I should see "Répartitions des dons"
