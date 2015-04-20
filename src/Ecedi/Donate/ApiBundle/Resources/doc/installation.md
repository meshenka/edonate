# Installing DonateApiBundle

## Enable the bundles
in app/AppKernel.php

Uncomment
```php
            // new FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            // new Ecedi\Donate\ApiBundle\DonateApiBundle(),
```

## Setup Configuration
in app/config/config.yml add fos_oauth_server (see src/Ecedi/Donate/ApiBundle/Resources/config/config.yml)

## Setup routing

in app/config/routing.yml uncomment

```yml
# donate_admin:
#     resource: "@DonateApiBundle/Resources/config/routing.xml"
#     prefix: /api/v1
```

## Setup firewall

Add firewall configuration (see src/Ecedi/Donate/ApiBundle/Resources/config/security.yml)


You are good to go! :D
