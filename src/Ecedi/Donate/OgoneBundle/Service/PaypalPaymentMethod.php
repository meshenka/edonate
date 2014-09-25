<?php

namespace Ecedi\Donate\OgoneBundle\Service;

use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\PaymentMethodInterface;
use Ecedi\Donate\CoreBundle\Entity\Intent;

/**
 * For test purpose
 */
class PaypalPaymentMethod implements PaymentMethodInterface
{

    public function getId()
    {
        return 'paypal';
    }

    public function getName()
    {
        return 'Paypal';
    }

    public function autorize(Intent $intent)
    {
        return false;
    }

    public function pay(Intent $intent)
    {
        return false;
    }

    public function getTunnel() {
        return self::TUNNEL_SPOT;
    }
}
