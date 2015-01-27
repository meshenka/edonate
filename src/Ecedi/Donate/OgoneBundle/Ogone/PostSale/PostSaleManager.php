<?php

/**
 * Cette class gère les post sales
 */
namespace Ecedi\Donate\OgoneBundle\Ogone\PostSale;

use Ecedi\Donate\OgoneBundle\Ogone\Response;
use Ecedi\Donate\OgoneBundle\Exception\UnauthorizedPostSaleException;
use Ecedi\Donate\OgoneBundle\Exception\CannotDetermineOrderIdException;
use Ecedi\Donate\CoreBundle\Entity\Payment;
use Symfony\Component\DependencyInjection\ContainerAware;
use Ecedi\Donate\CoreBundle\Event\DonateEvents;
use Ecedi\Donate\CoreBundle\Event\PaymentFailedEvent;
use Ecedi\Donate\CoreBundle\Event\PaymentCompletedEvent;
use Ecedi\Donate\CoreBundle\Event\PaymentDeniedEvent;
use Ecedi\Donate\CoreBundle\Event\PaymentCanceledEvent;
use Ecedi\Donate\CoreBundle\Event\PaymentAuthorizedEvent;

class PostSaleManager extends ContainerAware
{
    public function handle(Payment $payment)
    {
        return $this->doHandle($payment);
    }

        /**
         * traitement réel du paiement
         *
         * @param  Payment         $payment
         * @return PostSaleManager this object (for chaining)
         */
        protected function doHandle(Payment $payment)
        {
            $logger  = $this->container->get('logger');

            $normalizer = $this->container->get('donate_ogone.status_normalizer');
            $payment->setStatus($normalizer->normalize($payment->getResponse()->getStatus()));

                // START Donate\OgoneBundle specific code
                try {
                    //validate response
                        $this->validate($payment->getResponse());
                    $logger->debug('Payment Status : '.$payment->getStatus());
                } catch (UnauthorizedPostSaleException $e) {
                    $logger->warning('UnauthorizedPostSaleException');
                    $payment->setStatus(Payment::STATUS_INVALID);
                }
                // END Donate\OgoneBundle specific code

                //add payment to intent
                try {
                    $intentId  = $this->getIntentId($payment->getResponse());
                    $logger->debug('found intent id '.$intentId);
                } catch (CannotDetermineOrderIdException $e) {
                    $logger->warning('CannotDetermineOrderIdException');
                    $intentId = false;
                        //TODO le payment p-e ok, mais il est orphelin
                }

            $im = $this->container->get('donate_core.intent_manager');
            $im->attachPayment($intentId, $payment);

                //Throw events according to status
                switch ($payment->getStatus()) {
                    case Payment::STATUS_INVALID:
                        $this->container->get('event_dispatcher')->dispatch(DonateEvents::PAYMENT_FAILED, new PaymentFailedEvent($payment));
                        break;
                    case Payment::STATUS_CANCELED:
                        $this->container->get('event_dispatcher')->dispatch(DonateEvents::PAYMENT_CANCELED, new PaymentCanceledEvent($payment));
                        break;
                    case Payment::STATUS_PAYED:
                        $this->container->get('event_dispatcher')->dispatch(DonateEvents::PAYMENT_COMPLETED, new PaymentCompletedEvent($payment));
                        break;
                    case Payment::STATUS_DENIED:
                        $this->container->get('event_dispatcher')->dispatch(DonateEvents::PAYMENT_DENIED, new PaymentDeniedEvent($payment));
                        break;
                    case  Payment::STATUS_AUTHORIZED:
                        $this->container->get('event_dispatcher')->dispatch(DonateEvents::PAYMENT_AUTHORIZED, new PaymentAuthorizedEvent($payment));
                        break;

                }

            return $this;
        }

        /**
         * validation de la signature de la post-sale reçu
         * @param  Response                      $response
         * @return boolean                       true when signatire is valid
         * @throws UnauthorizedPostSaleException If signature is not valid
         */
        protected function validate(Response $response)
        {
            $sha1outkey =  $this->container->getParameter('donate_ogone.security.sha1_out');

            $keys = $response->jsonSerialize();

            ksort($keys);

            $hashKey = '';
            foreach ($keys as $key => $val) {
                if ($val != '' && $key != 'SHASIGN') {
                    $hashKey .= $key.'='.$val.$sha1outkey;
                }
            }

            $logger = $this->container->get('logger');
            $logger->debug('signature calculé :'.hash('sha1', $hashKey));
            $logger->debug('signature reçu :'.$response->getShasign());

            if (strtoupper(hash('sha1', $hashKey)) === strtoupper($response->getShasign())) {
                return true;
            }

            throw new UnauthorizedPostSaleException();
        }

        /**
         * Extract Intent Id from post-sale orderId
         *
         * @param  Response                        $response
         * @return integer                         the intent Id
         * @throws CannotDetermineOrderIdException If post-sale orderId does not match expected format
         */
        protected function getIntentId(Response $response)
        {
            $id = $response->getOrderId();
            $prefix =  $this->container->getParameter('donate_ogone.prefix');

            if (strpos($id, $prefix.'-') === 0) {
                return (int) str_replace($prefix.'-', '', $id);
            }

            throw new CannotDetermineOrderIdException();
        }
}
