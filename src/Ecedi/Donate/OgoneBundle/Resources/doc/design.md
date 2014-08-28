# Design features

La logique de DonateCore est de lancer des Events voir Ecedi\Donate\CoreBundle\Event\DonateEvents

DonateEvents::PAYMENT_REQUESTED est lancé dans le FrontBundle

TODO refactorer les PaymentMethod Plugins sous forme de listeners?

Quand la post-sale Arrive c'est la qu'est lancé DonateEvents::PAYMENT_RECEIVED

De base le controller récupère juste les infos de la post-sale et enregistre un Payment (non vérifié, non associé à un Intent)
(le status sera donc Payment::STATUS_NEW)


Ensuite tous les traitements se font via des Observateurs:

  * Ecedi\Donate\OgoneBundle\EventListener\ValidatePostSaleListener (observe DonateEvents::PAYMENT_RECEIVED)
    - va vérifier la signature de la post-sale, si la signature n'est pas valide, on passe le status à Payment::STATUS_FAILED
    - Dispatcher un event  DonateEvents::PAYMENT_FAILED
  * Ecedi\Donate\OgoneBundle\EventListener\PostSaleStatusListener (observe DonateEvents::PAYMENT_RECEIVED)
    - va convertir le response code d'Ogone en un status de Payment
    - Dispatcher un event en accord avec le nouveau Status
      * DonateEvents::PAYMENT_COMPLETED
      * DonateEvents::PAYMENT_FAILED
      * DonateEvents::PAYMENT_CANCELED
      * DonateEvents::PAYMENT_DENIED
  * Ecedi\Donate\OgoneBundle\EventListener\AttachIntentListener  (observe DonateEvents::PAYMENT_RECEIVED)
    - va ajouter le payment au bon Intent
  * Ecedi\Donate\OgoneBundle\EventListener\NotifyBadStatusListener (observe DonateEvents::PAYMENT_RECEIVED)
    - quand on reçoit une post-sale avec un status nécessitant une action humaine on envoie un email au VebMaster

  Enfin pour envoyer un email au donateur:
  * Ecedi\Donate\FrontBundle\EventListener\NotifyDonatorListener (observe DonateEvents::PAYMENT_COMPLETED)
    - noter que ca se trouve dans le front bundle, comme cela la customisation de l'email reste dans le front bundle.


Le bundle va devoir supporter le mode Event mais aussi le mode batch (en cas de multifrontaux ?)

En configuration batch, l'évent  DonateEvents::PAYMENT_RECEIVED n'est pas émis par le controller

La commande de traitement batch va donc retrouver les payment ayant le status Payment::STATUS_NEW et lancer l'event pour chacun


TODO comment basculer tous ces event sur kernel.terminate?