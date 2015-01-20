<?php

namespace Ecedi\Donate\CoreBundle\Exception;

class UnknownPaymentMethodException
extends \Exception
implements CoreExceptionInterface
{
    protected $paymentMethod;

    public function __contruct($pm)
    {
        $this->$paymentMethod = $pm;

        parent::__contruct('This payment method id is knowned');
    }
}
