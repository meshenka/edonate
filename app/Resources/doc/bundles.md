# Bundles

## Ecedi/Donate/CoreBundle
Ce bundle est le coeur du projet il comprends

* Le modèle de données
* L'API de service
* Les events customs


## Ecedi/Donate/AdminBundle
Ce bundle contient le back-office de l'application

* Administration des utilisateurs
* Configuration de formulaire de dons
  * Devises
  * Equivalences
  * Affectations
  * Calculette fiscale
  * Opt-in
* Configuration des emails
* Visualisation et export des dons et opt-ins

## Ecedi/Donate/ApiBundle
WebServices type REST pour l'interroperabilité
* Equivalences GET / PUT / PATCH / DELETE
* Devises GET / PUT / PATCH / DELETE
* Affecations GET / PUT / PATCH / DELETE
* Dons GET
* Payments GET / PUT / PATCH


## Ecedi/Donate/FrontBundle
Theme Front Office

## Ecedi/Donate/OgoneBundle
Interface vers Ogone
* Configuration, Post-Sale et Data Specifiques



