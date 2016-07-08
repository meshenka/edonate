<?php
/**
 * @author Alexandre Fayolle <afayolle@ecedi.fr>
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ecedi\Donate\PayboxBundle\EventListener;

use Lexik\Bundle\PayboxBundle\Event\PayboxResponseEvent;
use Ecedi\Donate\CoreBundle\IntentManager\IntentManagerInterface;
use Ecedi\Donate\CoreBundle\Entity\Payment;
use Ecedi\Donate\PayboxBundle\Model\IpnData;
use Ecedi\Donate\PayboxBundle\Paybox\StatusNormalizer;
use Psr\Log\LoggerInterface;

/**
 * Listener that manage Paybox IPN response.
 *
 * @since 2.2.0
 */
class IpnResponseListener
{
    /**
     * @var IntentManagerInterface
     */
    private $intentManager;

    /**
     * @var StatusNormalizer
     */
    private $normalizer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(IntentManagerInterface $intentManager, StatusNormalizer $normalizer, LoggerInterface $logger)
    {
        $this->intentManager = $intentManager;
        $this->normalizer = $normalizer;
        $this->logger = $logger;
    }
    /**
     * Handle paybox Response listener.
     *
     * @param PayboxResponseEvent $event
     */
    public function onPayboxIpnResponse(PayboxResponseEvent $event)
    {
        if ($event->isVerified()) {
            $this->logger->info('Verified Ipn received, payment is stored.');
            $ipnData = new IpnData($event->getData());

            $payment = new Payment();

            $payment->setAutorisation($ipnData->getAuthorisationId()) //n° autorisation
                    ->setTransaction($ipnData->getTransactionId()) //numéro transaction
                    ->setResponseCode($ipnData->getErrorCode()) //status paybox
                    ->setResponse($ipnData->getData())
                    ->setStatus($this->normalizer->normalize($ipnData->getErrorCode()));

            // On attache le paiement à l'intent
            $this->intentManager->attachPayment($ipnData->getIntentId(), $payment);

            return;
        }

        //unverified ipn... we just log it as warning
        //@TODO do something smartter to keep track of thoses
        $this->logger->warning('Unverified Ipn received, content is ignored.');
    }
}
