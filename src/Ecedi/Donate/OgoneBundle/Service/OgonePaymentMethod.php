<?php

namespace Ecedi\Donate\OgoneBundle\Service;

use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\AbstractPaymentMethod;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class OgonePaymentMethod extends AbstractPaymentMethod
{
    public function getId()
    {
        return 'ogone';
    }

    public function getName()
    {
        return 'Ogone';
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
            return new RedirectResponse($this->router->generate('donate_ogone_pay'));
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
