<?php

namespace Ecedi\Donate\CoreBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Ecedi\Donate\CoreBundle\Entity\Payment;

class PaymentEvent extends Event
{

    private $payment;

   /**
    * intent
    *
    * @return Intent intent
    */
   public function getPayment()
   {
       return $this->payment;
   }

   /**
    * intent
    *
    * @param Intent $newintent Intent
    */
   protected function setPayment(Payment $payment)
   {
       $this->payment = $payment;

       return $this;
   }

    public function __construct(Payment $payment)
    {
        $this->setPayment($payment);
    }
}
