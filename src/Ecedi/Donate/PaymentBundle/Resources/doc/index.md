# PaymentBundle

This bundle is a collection of standard supported Payment Methods

Currently it offers 
  * 'check_promise' donator is bound to print submitted intent and send by mail his donation

## CheckPromisePaymentMethod

It is a very simple offline/off-site payment method. Customer is supposed to print the page after his donation and send by mail to the given address

with his check.

This payment method is unable to track the actual payment


TODO
  * avoir une config d'équivalence par tunnel
  * avoir un block de montant par tunnel activé
  * gérer la relation entre les tunnels et les options de paiement