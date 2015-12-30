Feature: Test Front Pages

  @reset-schema
  @fixtures
  Scenario: Init data (create schema and load fixtures)
    Given I am on the homepage


  Scenario: Test as an anonymous user that I can access the homepage
    Given I go to the homepage
    Then the response status code should be 200
    And I should see "Association de test"

  Scenario Outline: Test Front End URLs as an anonymous
    Given I go to "<url>"
    Then the response status code should be <status>
    And I should see "<text>"

    Examples:
      | url                             | status | text                                  |
      | /fr/canceled                    | 200    | Vous avez annulé votre don            |
      | /en/canceled                    | 200    | You canceled your donation            |
      | /fr/denied                      | 200    | Votre don a été rejeté                |
      | /en/denied                      | 200    | Your donation was denied by your bank |
      | /fr/failed                      | 200    | Votre don a échoué                    |
      | /en/failed                      | 200    | Your donation failed                  |

    Scenario: Test submition for Check promise
      Given I am on the homepage
       When I select "TEST" from "donation[affectations]"
       And I fill in "donation_firstName" with "Unit"
       And I fill in "donation_lastName" with "Test"
       And I fill in "donation_email_first" with "behat@ecedi.fr"
       And I fill in "donation_email_second" with "behat@ecedi.fr"
       And I fill in "donation_addressStreet" with "77 rue du Paradis"
       And I fill in "donation_addressZipcode" with "75010"
       And I fill in "donation_addressCity" with "Paris"
       And I press "donation_payment_method_check_promise"
       Then the response status code should be 200
       And I should see "Merci pour votre don par chèque"

    Scenario: Test submition for CB spot payment with ogone
      Given I am on the homepage
       When I select "ALL" from "donation[affectations]"
       And I fill in "donation_firstName" with "Unit"
       And I fill in "donation_lastName" with "Test"
       And I fill in "donation_email_first" with "behat@ecedi.fr"
       And I fill in "donation_email_second" with "behat@ecedi.fr"
       And I fill in "donation_addressStreet" with "77 rue du Paradis"
       And I fill in "donation_addressZipcode" with "75010"
       And I fill in "donation_addressCity" with "Paris"
       And I press "donation_payment_method_ogone"
       Then the response status code should be 200
       And I should see "Dans quelques secondes, vous serez redirigé vers la page de paiement."


    Scenario: Test submition for Offline SEPA recurung payment
      Given I am on the homepage
       When I select "" from "donation[tunnels][spot][preselected]"
       And I select "10" from "donation[tunnels][recuring][preselected]"
       And I fill in "donation_firstName" with "Unit"
       And I fill in "donation_lastName" with "Test"
       And I fill in "donation_email_first" with "behat@ecedi.fr"
       And I fill in "donation_email_second" with "behat@ecedi.fr"
       And I fill in "donation_addressStreet" with "77 rue du Paradis"
       And I fill in "donation_addressZipcode" with "75010"
       And I fill in "donation_addressCity" with "Paris"
       And I press "donation_payment_method_sepa_offline"
       Then the response status code should be 200
       And I should see "Merci pour votre don par mandat de prélèvement SEPA"
