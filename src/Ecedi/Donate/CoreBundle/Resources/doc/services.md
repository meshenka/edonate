# Les services

## PaymentMethodInterface
Les services sont des API que doivent implémenter les méthodes de paiement pour fonctionner avec le core

  * Ecedi/Donate/CoreBundle/Service/PaymentMethodInterface - Interface des API de paiements

Le core vas découvrir les méthodes de paiements installés par des service implémentant l'interface PaymentMethodInterface et taggé donate.payment_method

La soumission d'un Order à une api de paiement des délégué à la PaymentMethod choisie pour cette commande

Order::getPaymentMethodId() -> id unique de la méthode de payment

Une méthode de paiement à un type:
  * onsite : Payment en ligne embarqué dans le formulaire
  * offsite : Payement en ligne mais déporté sur un autre site (cas Ogone)
  * offline : Payment hors ligne, cas des IBAN, RIB, Chèque, promesse de don

Grace au WebService Payment /api/v1/order/{{orderId}}/payment/ en PATCH on peut gérer en ligne la perception de l'argent.

Natif dans le core :
  * offline cheque : envoie de chèque, page à imprimer et expédier par la poste avec un chèque Ecedi/Donate/CheckBundle
  * offline pa-iban : envoie de don récurrent via IBAN, Document PDF à imprimer et expédier avec les info banquaire Ecedi/Donate/IbanBundle
  * offline promise : Promesse de don... ?


## PlaceholderService
Ce service est utiliser pour obtenir des tokens pour les template de mails

  PlaceholderService:replace(string $text, Payment $data)

Il va permettre de définir des Placeholder classique
ex 
  * [donator:civility]
  * [donator:fistname]
  * [donator:lastname]
  * [donator:email]
  * [order:amount]
  * [order:currency]
  * [order:type]
  * ...

Qui seront remplacé dans un texte
ex:

  $text = 'Bonjour [donator:civility] [donator:fistname] [donator:lastname]';

  $formattedText = $container->get('ecedi.donate.placeholder')->replace($text, $data);



# HOW TO

## HOWTO implémenter une nouvelle payment method

Il faut en premier créer un nouveau Bundle et l'activer. Disons SipsBundle

Dans ce Bundle il faut créer une classe de service
src/Demo/SipsBundle/Service/SipsPaymentMethod.php
``
namespace Demo\SipsBundle\Service;

use Ecedi\Donate\CoreBundle\Service\PaymentMethodInterface;

class SipsPaymentMethod implements PaymentMethodInterface
{
...
``

Puis dans services.xml on déclare le service et on le tag donate.payment_method

```
<container>
  <parameters>
    <parameter key="demo.sips_payment_method.class">Demo\SipsBundle\Service\SipsPaymentMethod</parameter>
  </parameters>
  <services>
    <service id="demo.sips_payment_method" class="%demo.sips_payment_method.class%">
      <tag name="donate.payment_method" />
    </service>
  </services>
</container>
```
