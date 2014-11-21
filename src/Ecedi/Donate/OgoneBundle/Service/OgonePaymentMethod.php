<?php

namespace Ecedi\Donate\OgoneBundle\Service;

use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\PaymentMethodInterface;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OgonePaymentMethod extends Controller implements PaymentMethodInterface
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
            return $this->redirect($this->generateUrl('donate_ogone_pay', []), 301);
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
