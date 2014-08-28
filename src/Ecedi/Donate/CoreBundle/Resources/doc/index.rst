# Fonctionalités en cours de discussion

## Mode Campagne
Il s'agit d'habiller le formulaire pendant un temps donnée

>>
  * Construir un nouveau theme, surcharger la/les templates
  * gérer le switch de theme dans config.yml

## Administration du header
Laisse le client avoir la main sur le header ?

>> Difficile à faire car la plateforme n'est pas un CMS
>> Nous charger de l'intégration devrait prendre 2h max de dev/iha

## Bandeau de droite
Fournir les fonctionnalités CMS pour administrer des blocs

>> symfony-cmf ?
>> le pb c'est que les zones administrable dépendent du zoning

## Mode de paiement ?
Peut ton gérer d'autre modes de paiement? Paypal, chèque, IBAN

>> C'est prévu via l'implémentation d'une PaymentMethodInterface
>> A voir les éléments configuration/administrable de ces méthodes

## Modification du fond de la page
Laisser le client administrer le fond de page?

>>Difficile en mode responsive  
>>Encore une fois il s'agit d'une option trop CMS à mon avis

## Mise à jour et monté de version

## WebService et Connecteur
Comment intégerer des services tiers ?

>> Le bundle Donate/ApiBundle devra répondre au besoin de récupération/maj des données


# Options techniques
* Séparer l'appli front et back-stats/reporting