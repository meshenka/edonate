<?php

namespace spec\Ecedi\Donate\OgoneBundle\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Ecedi\Donate\CoreBundle\Entity\Intent;
//use Symfony\Component\HttpFoundation\Response;
class OgonePaymentMethodSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Ecedi\Donate\OgoneBundle\Service\OgonePaymentMethod');
        $this->shouldHaveType('Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\PaymentMethodInterface');
    }

    function it_should_return_a_response(Intent $intent) {
    	
    	// $intent->getStatus()->willReturn(Intent::STATUS_NEW);
    	// $intent->getId()->willReturn(666);

    	$this->pay($intent)->shouldHaveType('Symfony\Component\HttpFoundation\Response');
    }
}
