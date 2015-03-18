<?php

namespace Ecedi\Donate\PayboxBundle\PaymentMethod\Plugin;

use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\AbstractPaymentMethod;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * A payment plugin for eCollect that plug DirectPayment with online/off-site TPE
 * @since  1.0.0
 */
class PayboxPaymentMethod extends AbstractPaymentMethod
{
    public function getId()
    {
        return 'paybox';
    }

    public function getName()
    {
        return 'Paybox';
    }

    /**
     * @TODO implement me
     */
    public function autorize(Intent $intent)
    {
        $response = new Response();
        $response->setStatusCode(500);

        return $response;
    }

    /**
     * return anything that can be managed as a response
     */
    public function pay(Intent $intent)
    {
        if ($intent->getStatus() === Intent::STATUS_NEW) {
            return new RedirectResponse($this->router->generate('donate_paybox_pay'));
        } else {
            $response = new Response();
            $response->setStatusCode(500);

            return $response;
        }
    }

    public function getTunnel()
    {
        return self::TUNNEL_SPOT;
    }
}