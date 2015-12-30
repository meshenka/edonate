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
      | /secured/intent/1/show                    | 200    |
      | /secured/customers                        | 200    |
      | /secured/customer/1/show                  | 200    |
      | /secured/customer/1/edit                  | 200    |
      | /secured/users                            | 200    |
      | /secured/user/1/edit                      | 200    |
      | /secured/user/2/edit                      | 200    |
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


  Scenario Outline: Test URLs as admin
    Given I am authenticated as "admin" on firewall "secured"
    When I go to "<url>"
    Then the response status code should be <status>

    Examples:
      | url                                       | status |
      | /secured/                                 | 200    |
      | /secured/intents                          | 200    |
      | /secured/intent/1/show                    | 200    |
      | /secured/customers                        | 200    |
      | /secured/customer/1/show                  | 200    |
      | /secured/customer/1/edit                  | 403    |
      | /secured/users                            | 200    |
      | /secured/user/1/edit                      | 200    |
      | /secured/user/2/edit                      | 200    |
      | /secured/user/new                         | 200    |
      | /secured/cms/layouts                      | 403    |
      | /secured/cms/layout/1/preview             | 403    |
      | /secured/cms/layout/1/blocks              | 403    |
      | /secured/cms/layout/1/affectations        | 403    |
      | /secured/cms/layout/1/affectations/add    | 403    |
      | /secured/cms/layout/1/affectations/1/edit | 403    |
      | /secured/cms/layout/1/affectations/add    | 403    |
      | /secured/cms/layout/1/edit                | 403    |
      | /secured/cms/layout/new                   | 403    |
      | /secured/cms/layout/1/block/1/edit        | 403    |

  Scenario Outline: Test URLs as CMS
    Given I am authenticated as "cms" on firewall "secured"
    When I go to "<url>"
    Then the response status code should be <status>

    Examples:
      | url                                       | status |
      | /secured/                                 | 200    |
      | /secured/intents                          | 200    |
      | /secured/intent/1/show                    | 200    |
      | /secured/customers                        | 200    |
      | /secured/customer/1/show                  | 200    |
      | /secured/customer/1/edit                  | 403    |
      | /secured/users                            | 403    |
      | /secured/user/1/edit                      | 403    |
      | /secured/user/3/edit                      | 403    |
      | /secured/user/new                         | 403    |
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

  Scenario Outline: Test URLs as affectation
    Given I am authenticated as "affectation" on firewall "secured"
    When I go to "<url>"
    Then the response status code should be <status>

    Examples:
      | url                                       | status |
      | /secured/                                 | 200    |
      | /secured/intents                          | 200    |
      | /secured/intent/1/show                    | 200    |
      | /secured/customers                        | 200    |
      | /secured/customer/1/show                  | 200    |
      | /secured/customer/1/edit                  | 403    |
      | /secured/users                            | 403    |
      | /secured/user/1/edit                      | 403    |
      | /secured/user/new                         | 403    |
      | /secured/cms/layouts                      | 403    |
      | /secured/cms/layout/1/preview             | 403    |
      | /secured/cms/layout/1/blocks              | 403    |
      | /secured/cms/layout/1/affectations        | 200    |
      | /secured/cms/layout/1/affectations/add    | 200    |
      | /secured/cms/layout/1/affectations/1/edit | 200    |
      | /secured/cms/layout/1/affectations/add    | 200    |
      | /secured/cms/layout/1/edit                | 403    |
      | /secured/cms/layout/new                   | 403    |
      | /secured/cms/layout/1/block/1/edit        | 403    |

  Scenario Outline: Test URLs as user
    Given I am authenticated as "user" on firewall "secured"
    When I go to "<url>"
    Then the response status code should be <status>

    Examples:
      | url                                       | status |
      | /secured/                                 | 200    |
      | /secured/intents                          | 200    |
      | /secured/intent/1/show                    | 200    |
      | /secured/customers                        | 200    |
      | /secured/customer/1/show                  | 200    |
      | /secured/customer/1/edit                  | 403    |
      | /secured/users                            | 403    |
      | /secured/user/1/edit                      | 403    |
      | /secured/user/new                         | 403    |
      | /secured/cms/layouts                      | 403    |
      | /secured/cms/layout/1/preview             | 403    |
      | /secured/cms/layout/1/blocks              | 403    |
      | /secured/cms/layout/1/affectations        | 403    |
      | /secured/cms/layout/1/affectations/add    | 403    |
      | /secured/cms/layout/1/affectations/1/edit | 403    |
      | /secured/cms/layout/1/affectations/add    | 403    |
      | /secured/cms/layout/1/edit                | 403    |
      | /secured/cms/layout/new                   | 403    |
      | /secured/cms/layout/1/block/1/edit        | 403    |
