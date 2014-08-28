# Fonctionnalités

  * Support multilangue (fr et en pour l'instant) - FAIT
  * Calculette fiscale - FAIT
  * Statistiques Google Analytics - FAIT
  * Le formulaire de don - FAIT
  * Bloc language switcher si plus d'une langue activé - FAIT
  * 4 pages de retours - FAIT
    * completed
    * canceled 
    * denied
    * failed
  * support de theme front office, - FAIT


# HOWTO
## Ajouter une nouvelle langue
. dans Resources/translations ajouter les nouveaux fichiers de traductions
. dans src/FrontBundle/Controller/ modifier les annotations pour ajouter le requirement sur la nouvelle langue.
. dans app/config/parameters.yml changer locale si nécessaire
. dans app/config/config.yml ajusté les valeurs de donate_front.i18n

## Configurer les civilités
dans app/config/config.yml modifier la config
``
donate_front:
   form:
        civility:
            mr: 'Mr'
            ms: 'Ms'
            mrs: 'Mrs'
            mis: 'Miss'
            pr: 'Prof'
            dr: 'Dr'
#           rev: 'Rev'
#           sir : 'Sir'
            cp: 'Company'
``

## Configurer les montants par défaut

Cela se fait dans la config de donate_core

## Configurer Google Analytics

dans app/config/parameters.yml modifié la ligne
``
    google_analytics:  ~
``
en
``
    google_analytics:  UA-XXXXXX
``

## Créer son propre theme
Il suffit de le déclarer dans config.yml

``
liip_theme:
    themes: ['default', 'montheme']
    active_theme: 'montheme'
``

Puis on peut créer un dossier app/Resources/views/theme/montheme et surcharger les templates de bundle dans ce dossier


ex: 
Dans ton cas tu aurai due créer ta template dans
app/Resources/views/theme/site_existant_2014/DonateFrontBundle/header.html.twig

et dans config.yml
``
liip_theme:
    themes: ['default', 'site_existant_2014']
    active_theme: 'site_existant_2014'
``

Et le fichier `app/Resources/views/theme/site_existant_2014/DonateFrontBundle/header.html.twig`

