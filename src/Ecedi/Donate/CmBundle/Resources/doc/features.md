# Fonctionnalités du Bundle

Lexique :
 @TODO chose à faire
 @FIXME chose à corriger/finir
 @TODISCUSS chose à discuter avant action

## Enregistrement des internautes dans campagn monitor

Ce Bundle permet l'enregistrement des internautes ayant coché la case optin dans campagn monitor.

C'est fait en commande ``php app/console donate:cm:push`` .

Cette commande s'appuie sur la table customer, champs optin et optinSynchronized.
Elle opère par lot via l'appel de l'API (https://github.com/campaignmonitor/createsend-php et http://www.campaignmonitor.com/api/).
Elle va chercher au maximum 200 internautes ayant optin True et optinSynchronized False, pousse via l'API CM dans la liste idoïne et si Ok met à True le champ optinSynchronized.

Il est nécessaire de configurer les variables campagn monitor, cf. configuration.md.

La commande ``php app/console donate:cm:reset`` permet de réinitialiser tous les optinSynchronized à False.


