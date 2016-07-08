<?php
/**
 * @author  Sylvain Gogel <sgogel@ecedi.fr>
 */
namespace Ecedi\Donate\CoreBundle\PaymentMethod\Plugin;

use Ecedi\Donate\CoreBundle\Entity\Intent;

/**
 * Base interface for PaymentMethod plugins
 * You have to implement this interface and register the class as
 * a service tagged payment_method and the magic will be done.
 *
 * @api
 *
 * @since  1.0.0
 */
interface PaymentMethodInterface
{
    const TUNNEL_RECURING = 'recuring';
    const TUNNEL_SPOT = 'spot';
    const TUNNEL_SPONSORSHIP = 'sponsorship';

    const PAYMENT_STATUS_COMPLETED = 'completed';
    const PAYMENT_STATUS_CANCELED = 'canceled';
    const PAYMENT_STATUS_DENIED = 'denied';
    const PAYMENT_STATUS_FAILED = 'failed';

    /**
     * Internal name of the payment method.
     *
     * @return string a unique identifier
     */
    public function getId();

    /**
     * Public name of the payment method.
     * Will be used in front office, must be translated.
     *
     * @return string Submit button value in front office form
     */
    public function getName();

    /**
     * Which payment tunnel should this Payment method be grouped in.
     *
     * @return string tunnel internal name
     */
    public function getTunnel();

    /**
     * recurring payment go in this payment process.
     *
     * @param Intent $intent intent to authorize (can be offline, online/in-site or online/off-site)
     *
     * @return Symfony\Component\HttpFoundation\Response an http response
     */
    public function autorize(Intent $intent);

    /**
     * spot payment go in this payment process.
     *
     * @param Intent $intent intent to use for payment
     *
     * @return Symfony\Component\HttpFoundation\Response an http response
     */
    public function pay(Intent $intent);
}
