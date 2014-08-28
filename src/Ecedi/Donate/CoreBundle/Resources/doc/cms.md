# Fonctionnalités CMS

## Layouts

Le layout représente les informations spécifique de la page:
  * meta title
  * meta description
  * meta keywords
  * baseline
  * logo
  * background

Au minimum il doit y avoir un layout par langue.

La possibilité de préparer d'autre layout permet de prévoir un habillage de campagne temporaire par exemple

### theme

la fonctionnalité de theme n'est pas encore totalement implémenté, il s'agit pour l'instant d'une simple class css ajouté sur le body

A terme il doit prévoir d'appliquer une feuille de style spécifique permettant la déclinaison graphique du gabarit en un clic.


## Blocks

Il y a une entité Block qui persiste les blocks éditoriaux

Block
  - id : id Doctrine
  - name : nom machine du block, non éditable
  - position : utilisé pour controller l'ordre d'affichage des blocks
  - type : constante, non éditable, non utilisé pour l'instant
  - enabled : boolean, utilisé pour caché un block
  - title : titre du block
  - body : textarea + WYSIWYG

Les blocks sont des blocs éditoriaux libre, associé à une layout spécifique.

On ne peut pas pour l'instant réutiliser un bloc dans plusieurs layout.

TODO voir comment controler une classe CSS à mettre sur chaque bloc ?

## Front Office

Dans le FrontController nous avons un SidebarController qui genère le code html de la sidebar

ce controller sans route est invoké depuis le layout

```
<aside id="sidebar">
	{% block sidebar %}
    	{{ render_esi(controller('DonateFrontBundle:Sidebar:show')) }}
    {% endblock %}
</aside>
```


## Back Office

path : cms/layouts affiche la liste des layouts (les etoile noir indique les layouts par défauts)


Une commande GenerateBlockCommand initialise les entities est à lancer après l'installation

```
app/console donate:generate:block
```

TODO la commande ne doit pas créer des blocks en doublons, les Block:name sont sensé etre unique pour une langue donnée