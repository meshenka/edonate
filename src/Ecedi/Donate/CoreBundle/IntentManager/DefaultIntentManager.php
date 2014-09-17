<?php

namespace Ecedi\Donate\CoreBundle\IntentManager;

use Ecedi\Donate\CoreBundle\Entity\Intent;
use Ecedi\Donate\CoreBundle\Entity\Payment;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Ecedi\Donate\CoreBundle\Event\DonateEvents;
use Ecedi\Donate\CoreBundle\Event\DonationRequestedEvent;
use Ecedi\Donate\CoreBundle\Event\PaymentRequestedEvent;

class DefaultIntentManager implements IntentManagerInterface
{
    private $discovery;
    private $container;
    private $logger;

    protected function getDoctrine()
    {
        if (!$this->container->has('doctrine')) {
            throw new \LogicException('The DoctrineBundle is not registered in your application.');
        }

        return $this->container->get('doctrine');
    }

    protected function preHandle() {

            $request = $this->container->get('request');
            $session = $request->getSession();
            $session->set('intentId', $intent->getId());

            // try to see if the locale has been set as a _locale routing parameter
            if ($locale = $request->getLocale()) {
                $session->set('_locale', $locale);
            }

    }

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->discovery = $container->get('donate_core.payment_method_discovery');
        $this->logger = $container->get('logger');
    }

    public function handleAutorize(Intent $intent)
    {
        $this->preHandle();
        //find used PaymentMethod and send if
        $pm = $this->discovery->getMethod($intent->getPaymentMethod());

        return $pm->autorize($intent);
    }

    public function handlePay(Intent $intent)
    {
        $this->preHandle();
        //find used PaymentMethod and send if
        $pm = $this->discovery->getMethod($intent->getPaymentMethod());

        if($pm) {
            $this->container->get('event_dispatcher')->dispatch(DonateEvents::PAYMENT_REQUESTED, new PaymentRequestedEvent($intent));
            return $pm->pay($intent);            
        }

        return ''; //TODO return a 404 response


    }

    /**
     * Set status as pending
     */
    public function pending(Intent $intent)
    {
        $intent->setStatus(Intent::STATUS_PENDING);

        $em = $this->getDoctrine()->getManager();
        $em->persist($intent);
        $em->flush();

        return $this;
    }

    /**
     * Initialisation d'un nouvel intent
     */
    public function newIntent($amount, $paymentMethodId)
    {
        $intent = new Intent($amount, $paymentMethodId);

        $this->container->get('event_dispatcher')->dispatch(DonateEvents::DONATION_REQUESTED, new DonationRequestedEvent($intent));

        return $intent;
    }

    /**
     * Association d'un Intent et d'un paiement, avec envoie des evenements
     * @param  mixed                                  $intentId integer si intentId est false alors nous avons un payment orphelin.
     * @param  Ecedi\Donate\CoreBundle\Entity\Payment $payment  une instance de payment
     * @return none
     */
    public function attachPayment($intentId, Payment $payment)
    {
        $intentRepository = $this->getDoctrine()->getRepository('DonateCoreBundle:Intent');

        $em = $this->getDoctrine()->getManager();

        if ($intentId && $intent = $intentRepository->find($intentId)) {
            $intent->addPayment($payment);
            $this->logger->debug('addPayment to intent');
            $payment->setIntent($intent);
            $this->logger->debug('Set Intent on payment');

            if ($intent->getType() == Intent::TYPE_SPOT && $intent->getStatus() == Intent::STATUS_PENDING) {
                //Propagation de l'état du paiement vers l'intent
                $intent->setStatus(Intent::STATUS_DONE);
                $this->logger->debug('set intent status to DONE');

            } else {
                //on reçoit plusieurs post-sale pour le même spot order...
                $this->logger->notice('another post sale for this intent');

            }

            $em->persist($intent);
        }

        $em->persist($payment);
        $em->flush();
    }

}
