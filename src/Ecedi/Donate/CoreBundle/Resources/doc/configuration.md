Le bundle supporte la configuration suivante

donate_core:
  mail:
  	donator : true|false # envoi d'un email au donateur
  	webmaster : [email1, email2, ...] # email vers le webmaster quand on a un don ok, laisser vide pour ne pas recevoir de mail
    equivalence:
        - {amount : 10, label : '10 euros'}
        - {amount : 20, label : '20 euros'}
        - {amount : 50, label : '50 euros'}


# HOWTO

## Configurer les équivalences de dons

Dans config.yml

``
donate_core:
    equivalence:
        - {amount : 10, label : '10 euros'}
        - {amount : 20, label : '20 euros'}
        - {amount : 50, label : '50 euros'}
 ``

On peut définir autant d'équivalence qu'on le souhaite.
Attention les labels doivent être en Anglais si on active le support multilangue