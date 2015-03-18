<?php
/**
 * @author Alexandre Fayolle <afayolle@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @package Ecollecte
 */

namespace Ecedi\Donate\PayboxBundle\EventListener;

use Lexik\Bundle\PayboxBundle\Event\PayboxResponseEvent;
use Ecedi\Donate\CoreBundle\IntentManager\IntentManagerInterface;
use Ecedi\Donate\CoreBundle\Entity\Payment;
use Ecedi\Donate\PayboxBundle\Model\IpnData;
use Ecedi\Donate\PayboxBundle\Paybox\StatusNormalizer;
/**
 * Listener that manage Paybox IPN response
 *
 * @since 2.2.0
 */
class IpnResponseListener
{
    private $intentManager;
    private $normalizer;

    public function __construct(IntentManagerInterface $intentManager, StatusNormalizer $normalizer)
    {
        $this->IntentManager = $intentManager;
        $this->normalizer = $normalizer;
    }
    /**
     * Handle paybox Response listener
     *
     * @param PayboxResponseEvent $event
     */
    public function onPayboxIpnResponse(PayboxResponseEvent $event)
    {
        $ipnData = new IpnData($event->getData());

        $payment = new Payment();

        $payment->setAutorisation($ipnData->getAuthorisationId()) //n° autorisation
                ->setTransaction($ipnData->getTransactionId()) //numéro transaction
                ->setResponseCode($ipnData->getErrorCode()) //status paybox
                ->setResponse($ipnData->getData())
                ->setStatus($this->normalizer->normalize($ipnData->getErrorCode());

        // On attache le paiement à l'intent
        $this->intentManager->attachPayment($ipnData->getIntentId(), $payment);

        return $payment;
    }
}
