# HOW TO extends eDonate

3 ways to extends
  * customize styles
  * customize templates
  * customize data model

## HOW TO customize styles

The simple thing to do is to adapt css styles

To do so you can introduce your own css in
  * app/Resources/assets/css/front-header/[custom].{less|sass|css} -- styles for front office what will go in the header
  * app/Resources/assets/css/front-footer/[custom].{less|sass|css} -- styles for front office what will go in the footer
  * app/Resources/assets/css/admin-header/[custom].{less|sass|css} -- styles for back office what will go in the footer
  * app/Resources/assets/css/admin-footer/[custom].{less|sass|css} -- styles for back office what will go in the footer
  * app/Resources/assets/js/front/[custom].{js} -- javascript for front office
  * app/Resources/assets/js/admin/[custom].{js} -- javascript fot back office

Tous ces fichiers seront agglomérés par glup et envoyé dans, respectivement
  * web/css/build/front-header.css
  * web/css/build/front-footer.css
  * web/css/build/admin-header.css
  * web/css/build/admin-footer.css
  * web/js/build/admin.js
  * web/js/build/front.js (ex main.js)

Resources like fonts should be placed in
  * app/Resources/assets/fonts

Medias goes in
  * app/Resources/assets/images/


gulp will automaticaly taking thoses into account and produce update compile


## HOW TO customize templates
If you need to adapt html markup the easiest way is to use standard template behavors

@see http://symfony.com/fr/doc/current/book/templating.html#overriding-bundle-templates

If you want to override DonateFrontBundle:Form:index.html.twig

Create template in app/Resources/DonateFrontBundle/views/Form/index.html.twig

Any template can be overridden by this way


## HOW TO customize data model

This is a more advanced topic. Modifiy data model will imply lot of modification in PHP
  * Extends and Doctrine map custom model
  * Extends FormTypes
  * Change templating

TODO find a way to do it!!


