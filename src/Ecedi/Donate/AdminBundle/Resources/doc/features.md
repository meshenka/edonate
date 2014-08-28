# Fonctionnalités du Bundle

Lexique :
 @TODO chose à faire
 @FIXME chose à corriger/finir
 @TODISCUSS chose à discuter avant action

## Gestion des comptes et authentification

Le Bundle gère l'accès aux back office, il force l'internaute à s'authentifier pour avoir accès aux écrans BO

On distingue les roles suivants :
  * SUPER_ADMIN accès à tous
  * ADMIN accès aux écrans de gestion/création de users
  * USER accès aux écrans de reportings


Les écrans sont les suivants ;
  * /users liste des utilisateurs,
  * /user/{userId}/edit edition d'un utilisateur, avec possibilité de bloquer un compte, et définition des roles
    * On en peut pas se supprimer soit même
    * Un ADMIN ne peut pas éditer un ROOT

Globalement il va simplement servire au client à créer des comptes pour accèder aux reportings

## Configuration du Formulaire de don
(à confirmer)
Certains éléments du formulaire de don, comme les template de mails sont à configurer dans le BO

 Ca implique la gestion des entity suivantes (défini dans le CoreBundle)
 DonateFormSetting, MailTemplate, Contact

   * /settings/forms ->liste des DonateFormSetting
   * /settings/form/{formSettingId} -> edition d'un form setting
   * /settings/contacts
   * /settings/contact/{contactId}   
   * /settings/mails
   * /settings/mail/{mailId}

NOTA : A faire plus tard


## Reporting
Ces écrans permettent aux utilisateurs d'avoir des reportings.

!! Tous ces écrans sont R/O. Nous ne pouvons pas éditer les données (à confirmer)

  * /reporting/payment/{paymentId} -> détail d'un paiement (avec désérialisation de la réponse ??)
  * /reporting/intents --> liste des promesses de dons
    * Liste
    * Tries
    * Filtre
    * Pagniation
  * /reporting/intent/{intentId} --> détail d'une promesse
    * Affichage de l'Intent
    * Affichage et navigation vers le customer
    * Affichage résumé des payments recu pour cet intent, navigation vers le payment
  * /reporting/customers --> liste des donateurs
    * Liste
    * Tries
    * Filtre
    * Pagniation
  * /reporting/customer/{customerId} -->Detail d'un donateurs
    * Info complête sur le donateur
    * Liste des Intents en affichage résumé
    * Recherche de doublons
      * afficher une liste de donateurs qui nous semble identitique à celui ci
      * TODISCUSS : proposer une fonction de dédoublonnage

### Dédoublonnage des Customer
TODISCUSS

on pourrai mettre en place une fonction qui recherche les doublons, par email et voit si on pourrai pas les fusionner
TODO définir les modalité de l'algo d'identification de doublons

### Les Exports

Nous ne mettons à disposition que des exports mois par mois des dons

NOTA: Pour des raisons de conso mémoire il est important de limiter le nombre d'items à exporter.

Il doit être possible d'exporter :
  * Tous les donateurs, avec filtres actifs
  * Tous les Intents, avec filtres actifs
  * Exporter le résultat d'une recherche sur les intents si il y a - de 5000 résultats (mettre ce param configurable) ?

Enfin une commade doit être disponible pour l'export d'intent mois par mois

  app/console donate:export:intent -1
  app/console donate:export:intent 2012-12

  * -1 indique qu'il faut prendre le mois courant -1, donc si on lance cette commande le 18 nov 2013, il va prendre tous les intents entre le 1 oct 2013 et le 31 octobre 2013
  * On peut mettre 0 pour exporter les dons entre le 1 nov 2013 et le 18 nov 2014
  * On peut mettre -2, -3 etc...
  * on peut indiquer 2010-01 pour exporter janvier 2010



