# PaymentMethods

(design document) 

PaymentMethods are plugable services that implement PaymentMethodInterface

each payment method is responsible for specifics of each TPE

## Tunnels ?

Donations works with two Sell tunnels:
  * spot tunnel for donators that wants to do a oneshot payment
  * recuring tunnel for donators that wants to send payment each month (of various frequency, TBD)

Each Tunnel got specifics PaymentMethods and Equivalences

You should be able to enable one or the other or both tunnels

Each Tunnels send Intent to specifics PaymentMethods, for instance even if Ogone TPE allows for spot or recurring you will need two PaymentMethods plugins
  * OgoneDirectPaymentMethod will do inline payment on the way
  * OgoneBatchPaymentMethod will register CB aliases and process batch uploads on defined frequency

The Spot Tunnel use a Payment process on form submission, while Recurring process will
