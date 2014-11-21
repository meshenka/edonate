<?php

namespace Ecedi\Donate\CoreBundle\IntentManager;

use Ecedi\Donate\CoreBundle\Entity\Intent;
use Ecedi\Donate\CoreBundle\Entity\Payment;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Ecedi\Donate\CoreBundle\Event\DonateEvents;
use Ecedi\Donate\CoreBundle\Event\DonationRequestedEvent;
use Ecedi\Donate\CoreBundle\Event\PaymentRequestedEvent;
use Ecedi\Donate\CoreBundle\Event\AutorizationRequestedEvent;
use Ecedi\Donate\CoreBundle\Exception\UnknownPaymentMethodException;
use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\PaymentMethodInterface;

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

    protected function preHandle(Intent $intent)
    {
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

    /**
     * run autorize or pay according to tunnel
     *
     * @param  Intent                        $intent [description]
     * @return [type]                        [description]
     * @throws UnknownPaymentMethodException If method id is not found in existing configuration
     */
    public function handle(Intent $intent)
    {
        $this->preHandle($intent);
        $pm = $this->discovery->getMethod($intent->getPaymentMethod());
        if ($pm) {
            if (($pm->getTunnel() === PaymentMethodInterface::TUNNEL_RECURING) || ($pm->getTunnel() === PaymentMethodInterface::TUNNEL_SPONSORSHIP)) {
                return $this->handleAutorize($intent);
            }

            if ($pm->getTunnel() === PaymentMethodInterface::TUNNEL_SPOT) {
                return $this->handlePay($intent);
            }
        } else {
            throw new UnknownPaymentMethodException($intent->getPaymentMethod());
        }
    }

    /**
     * run autorisation for recurring sell tunnel
     *
     * @param  Intent   $intent
     * @return Response
     */
    protected function handleAutorize(Intent $intent)
    {
        //find used PaymentMethod and send if
        $pm = $this->discovery->getMethod($intent->getPaymentMethod());

        $this->container->get('event_dispatcher')->dispatch(DonateEvents::AUTORIZATION_REQUESTED, new AutorizationRequestedEvent($intent));

        return $pm->autorize($intent);
    }

    /**
     * run immediate payment for spot sell tunnel
     *
     * @param  Intent                        $intent
     * @return Response
     * @throws UnknownPaymentMethodException If method id is not found in existing configuration
     */
    protected function handlePay(Intent $intent)
    {
        //find used PaymentMethod and send if
        $pm = $this->discovery->getMethod($intent->getPaymentMethod());

        $this->container->get('event_dispatcher')->dispatch(DonateEvents::PAYMENT_REQUESTED, new PaymentRequestedEvent($intent));

        return $pm->pay($intent);
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
