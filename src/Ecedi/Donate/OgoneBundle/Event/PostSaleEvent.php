<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 */
namespace Ecedi\Donate\OgoneBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Ecedi\Donate\OgoneBundle\Ogone\Response;
use Ecedi\Donate\CoreBundle\Entity\Payment;

/**
 * Ogone Post Sale Hook event.
 *
 * @since  2.2.0 new class
 */
class PostSaleEvent extends Event
{
    /**
     * @var Response
     */
    private $response;

    /**
     * @var Payment
     */
    private $payment;

    /**
     * get payment.
     *
     * @return Payment the payment entity
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * set payment.
     *
     * @param Payment $newpayment The payment entity
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * intent.
     *
     *  @return Intent intent
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function __construct(Response $response)
    {
        $this->response = $response;
    }
}
