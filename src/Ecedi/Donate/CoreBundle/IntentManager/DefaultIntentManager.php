<?php
/**
 * @author  Sylvain Gogel <sgogel@ecedi.fr>
 * @package eDonate
 * @subpackage Core
 * @copyright Agence Ecedi 2014
 *
 */
namespace Ecedi\Donate\CoreBundle\IntentManager;

use Ecedi\Donate\CoreBundle\Entity\Intent;
use Ecedi\Donate\CoreBundle\Entity\Payment;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Ecedi\Donate\CoreBundle\Event as Ev;
use Ecedi\Donate\CoreBundle\Exception\UnknownPaymentMethodException;
use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\PaymentMethodInterface;

/**
 * Default implementation of the IntentManagerInterface
 * @since  1.0.0
 */
class DefaultIntentManager implements IntentManagerInterface
{
    /**
     * The service that gather and manager all payment methods
     *
     * @var Ecedi\Donate\CoreBundle\PaymentMethod\Discovery
     */
    private $discovery;

    /**
     * The Symfony Container
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * a PSR-3 logger service
     * @var Psr\Log\LoggerInterface
     */
    private $logger;

    protected function getDoctrine()
    {
        if (!$this->container->has('doctrine')) {
            throw new \LogicException('The DoctrineBundle is not registered in your application.');
        }

        return $this->container->get('doctrine');
    }

    /**
     * Business Logic to run prior entering any Payment method
     *
     * @since 2.0.0
     * @param  Intent $intent [description]
     * @return [type] [description]
     */
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

    /**
     * @since 1.0.0
     * @param ContainerInterface $container a configured container
     * @todo  do not inject the container, use explicit dependencies
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->discovery = $container->get('donate_core.payment_method_discovery');
        $this->logger = $container->get('logger');
    }

    /**
     * {@inheritdoc}
     * @throws UnknownPaymentMethodException if not payment methods can handle this intent
     */
    public function handle(Intent $intent)
    {
        $this->preHandle($intent);
        $paymentMethod = $this->discovery->getMethod($intent->getPaymentMethod());
        if ($paymentMethod) {
            if (($paymentMethod->getTunnel() === PaymentMethodInterface::TUNNEL_RECURING) || ($paymentMethod->getTunnel() === PaymentMethodInterface::TUNNEL_SPONSORSHIP)) {
                return $this->handleAutorize($intent);
            }

            if ($paymentMethod->getTunnel() === PaymentMethodInterface::TUNNEL_SPOT) {
                return $this->handlePay($intent);
            }
        }
        throw new UnknownPaymentMethodException($intent->getPaymentMethod());
    }

    /**
     * Run autorisation for recurring sell tunnel
     *
     * @since  2.0.0
     * @param  Intent                                    $intent
     * @return Symfony\Component\HttpFoundation\Response
     */
    protected function handleAutorize(Intent $intent)
    {
        //find used PaymentMethod and send if
        $paymentMethod = $this->discovery->getMethod($intent->getPaymentMethod());

        $this->container->get('event_dispatcher')->dispatch(Ev\DonateEvents::AUTORIZATION_REQUESTED, new Ev\AutorizationRequestedEvent($intent));

        return $paymentMethod->autorize($intent);
    }

    /**
     * run immediate payment for spot sell tunnel
     *
     * @param  Intent                                    $intent
     * @return Symfony\Component\HttpFoundation\Response
     * @since  2.0.0
     */
    protected function handlePay(Intent $intent)
    {
        //find used PaymentMethod and send if
        $paymentMethod = $this->discovery->getMethod($intent->getPaymentMethod());

        $this->container->get('event_dispatcher')->dispatch(Ev\DonateEvents::PAYMENT_REQUESTED, new Ev\PaymentRequestedEvent($intent));

        return $paymentMethod->pay($intent);
    }

    /**
     * Set status as pending
     * @since  1.0.0
     * @todo  should not flush here, flush should be in controller
     * @todo  i'm not even sure this method should stay, should probably move to entity class
     * @param  Intent an intent
     * @return current object for chainability
     * @deprecated Deprecated since version 2.2.0, to be removed in 2.4.0. Use $intent->setStatus(Intent::STATUS_PENDING) directly instead.
     */
    public function pending(Intent $intent)
    {
        $intent->setStatus(Intent::STATUS_PENDING);

        $em = $this->getDoctrine()->getManager();
        $em->persist($intent);
        $em->flush();
        trigger_error('pending() is deprecated since version 2.2.0, to be removed in 2.4.0. Use $intent->setStatus(Intent::STATUS_PENDING) directly instead.', E_USER_DEPRECATED);

        return $this;
    }

    /**
     * Initialisation d'un nouvel intent
     * This method is usefull as it dispatch an event
     * @todo  may be should be moved somewhere else, like in a Doctrine Event listener as recommanded by Kris Wallsmith
     * @see  https://www.youtube.com/watch?v=W8MOIOyPbmM
     *
     * @since  1.0.0
     *
     * @param  integer amount
     * @param string the unique machine name that indiquate which payment method should handle
     * @return a new Intent instance
     */
    public function newIntent($amount, $paymentMethodId)
    {
        $intent = new Intent($amount, $paymentMethodId);

        $this->container->get('event_dispatcher')->dispatch(Ev\DonateEvents::DONATION_REQUESTED, new Ev\DonationRequestedEvent($intent));

        return $intent;
    }

    /**
     * dispatch event according to payment status
     *
     * @since 2.2.0
     * @param Payemnt $payment a payment instance
     */
    protected function dispatchPaymentStatusEvent($payment)
    {
        //Throw events according to status
        // @since 2.2.0
        switch ($payment->getStatus()) {
            case Payment::STATUS_INVALID:
                $this->container->get('event_dispatcher')->dispatch(Ev\DonateEvents::PAYMENT_FAILED, new Ev\PaymentFailedEvent($payment));
                break;
            case Payment::STATUS_CANCELED:
                $this->container->get('event_dispatcher')->dispatch(Ev\DonateEvents::PAYMENT_CANCELED, new Ev\PaymentCanceledEvent($payment));
                break;
            case Payment::STATUS_PAYED:
                $this->container->get('event_dispatcher')->dispatch(Ev\DonateEvents::PAYMENT_COMPLETED, new Ev\PaymentCompletedEvent($payment));
                break;
            case Payment::STATUS_DENIED:
                $this->container->get('event_dispatcher')->dispatch(Ev\DonateEvents::PAYMENT_DENIED, new Ev\PaymentDeniedEvent($payment));
                break;
            case  Payment::STATUS_AUTHORIZED:
                $this->container->get('event_dispatcher')->dispatch(Ev\DonateEvents::PAYMENT_AUTHORIZED, new Ev\PaymentAuthorizedEvent($payment));
                break;

        }
    }
    /**
     * Association d'un Intent et d'un paiement, avec envoie des evenements
     * @since  1.0.0
     * @param mixed                                  $intentId integer si intentId est false alors nous avons un payment orphelin.
     * @param Ecedi\Donate\CoreBundle\Entity\Payment $payment  une instance de payment
     * @todo  flush is not right here, it should be in the controller
     *
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

        $this->dispatchPaymentStatusEvent($payment);

        $em->persist($payment);
        $em->flush();
    }
}
