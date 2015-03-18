# DonatePayboxBundle

The bundle is a glu code between LexikPayboxBundle and ECollect application

@see official doc for more details http://www1.paybox.com/espace-integrateur-documentation/la-plateforme-de-tests/

## Enabling the bundle

add dependencies to composer.json

```
   "require": {
        "lexik/paybox-bundle": "dev-master"
    }
```

enable bundles in app/AppKernel.php
```
    new Ecedi\Donate\PayboxBundle\DonatePayboxBundle(),
    new Lexik\Bundle\PayboxBundle\LexikPayboxBundle(),
```

Add to app/config/parameters.yml-dist
```
    paybox.production: false
    paybox.site: '9999999'
    paybox.rank: '99'
    paybox.login: '999999999'
    paybox.hmac.key: '01234...BCDEF'
```

add to app/config/config.yml
```
lexik_paybox:
    parameters:
        production: %paybox.production%
        site:        %paybox.site%   # Site number provided by the bank
        rank:        %paybox.rank%        # Rank number provided by the bank
        login:       %paybox.login% # Customer's login provided by Paybox
        hmac:
            key: %paybox.hmac.key% # Key used to compute the hmac hash, provided by Paybox
```

Import routes in app/config/routing.yml
```
donate_paybox:
    resource: "@DonatePayboxBundle/Controller/"
    type:     annotation
    prefix:   /paybox

lexik_paybox:
    resource: "@LexikPayboxBundle/Resources/config/routing.yml"
    prefix:   /paybox
```

run ``composer install``

Now paybox is an available Payment Method for your ecollect

## Configuring the bundle

To make this payment method available, just enable it in app/config/ecollect.yml

```
donate_core:
    payment_methods: ['paybox']
```
