<?php

namespace Ecedi\Donate\OgoneBundle\Ogone\PostSale;

use Ecedi\Donate\CoreBundle\Entity\Payment;

class MemorySpoolPostSaleManager extends PostSaleManager
{
    private $spool;

    public function __construct()
    {
        $this->spool = [];
    }

    public function handle(Payment $payment)
    {
        $this->spool[] = $payment;

        return $this;
    }

    public function flush()
    {
        foreach ($this->spool as $payment) {
            $this->doHandle($payment);
        }

        $this->spool = []; //empty the spool
        $logger  = $this->container->get('logger');
        $logger->info('PostSale spool flushed');
    }
}
