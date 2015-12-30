Feature: Test secured url response code

  @reset-schema
  @fixtures
  Scenario: Init data (create schema and load fixtures)


  Scenario Outline: Test URLs as root
    Given I am authenticated as "root" on firewall "secured"
    When I go to "<url>"
    Then the response status code should be <status>

    Examples:
      | url                                       | status |
      | /secured/                                 | 200    |
      | /secured/intents                          | 200    |
      | /secured/intent/1/show                   | 200    |
      | /secured/customers                        | 200    |
      | /secured/customer/1/show                  | 200    |
      | /secured/customer/1/edit                  | 200    |
      | /secured/users                            | 200    |
      | /secured/user/1/edit                      | 200    |
      | /secured/user/new                         | 200    |
      | /secured/cms/layouts                      | 200    |
      | /secured/cms/layout/1/preview             | 200    |
      | /secured/cms/layout/1/blocks              | 200    |
      | /secured/cms/layout/1/affectations        | 200    |
      | /secured/cms/layout/1/affectations/add    | 200    |
      | /secured/cms/layout/1/affectations/1/edit | 200    |
      | /secured/cms/layout/1/affectations/add    | 200    |
      | /secured/cms/layout/1/edit                | 200    |
      | /secured/cms/layout/new                   | 200    |
      | /secured/cms/layout/1/block/1/edit        | 200    |
