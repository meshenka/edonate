<?php

namespace Ecedi\Donate\PayboxBundle\EventListener;

use Lexik\Bundle\PayboxBundle\Event\PayboxResponseEvent;
use Ecedi\Donate\CoreBundle\IntentManager/IntentManagerInterface;
use Ecedi\Donate\CoreBundle\Entity\Payment;
use Ecedi\Donate\PayboxBundle\Paybox\PayboxResponseManager;

/**
 * Listener that manage Paybox IPN response
 *
 * @author Alexandre FAYOLLE <afayolle@ecedi.fr>
 */
class IpnResponseListener {

    private $intentManager;

    public function __construct(IntentManagerInterface $intentManager) {
        $this->IntentManager = $intentManager;
    }
    /**
     * Handle paybox Response listener
     *
     * @param PayboxResponseEvent $event
     */
    public function onPayboxIpnResponse(PayboxResponseEvent $event)
    {
        $responseManager = new PayboxResponseManager($event);
        $payment = $this->handlePayment($responseManager);
    }
    /**
     * Handle paybox Response listener
     *
     * @param PayboxResponseManager $responseManager
     */
    private function handlePayment(PayboxResponseManager $responseManager)
    {
        $payment = new Payment();

        $payment->setAutorisation($responseManager->getAuthorisationId()) //n° autorisation
                ->setTransaction($responseManager->getTransactionId()) //numéro transaction
                ->setResponseCode($responseManager->getErrorCode()) //status paybox
                ->setResponse($responseManager->getData())
                ->setStatus($responseManager->getPaymentStatus());

        // On attache le paiement à l'intent
        $this->intentManager->attachPayment($responseManager->getIntentId(), $payment);

        return $payment;
    }
}
