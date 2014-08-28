<?php

namespace spec\Ecedi\Donate\OgoneBundle\Ogone;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use  Symfony\Component\HttpFoundation\Request;

class ResponseSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Ecedi\Donate\OgoneBundle\Ogone\Response');
    }

  //   function it_is_initializable_via_globals() {

  //   	global $_GET; 
  //   	$_GET = array('a' => 'b');
  //   	global $_POST; 
  //   	$_POST = array('d' => 'd');

  //   	$response = $this->createFromGlobals();
  //   	$response->shouldHaveType('Ecedi\Donate\OgoneBundle\Ogone\Response');
  //   	$response->getQuery()->shouldHaveType('Symfony\Component\HttpFoundation\ParameterBag');
  //   	$response->getRequest()->shouldHaveType('Symfony\Component\HttpFoundation\ParameterBag');
		
		// $response->getQuery()->shouldHaveCount(1);
		// $response->getRequest()->shouldHaveCount(1);
  //   }

    function it_is_initializable_via_request(Request $request) {

        $this->trainRequest($request);

        $response = $this->createFromRequest($request);
        $response->shouldHaveType('Ecedi\Donate\OgoneBundle\Ogone\Response');
        
  //       $response->getQuery()->shouldHaveType('Symfony\Component\HttpFoundation\ParameterBag');
  //    $response->getRequest()->shouldHaveType('Symfony\Component\HttpFoundation\ParameterBag');
        
        // $response->getQuery()->shouldHaveCount(1);
        // $response->getRequest()->shouldHaveCount(2);
    }

    protected function trainRequest(Request $request) {
        $request->get('orderID')->willReturn('SPEC-17');
        $request->get('amount')->willReturn(10000);
        $request->get('currency')->willReturn('EUR');
        $request->get('PM')->willReturn('CreditCard');
        $request->get('ACCEPTANCE')->willReturn('auth-6+454564');
        $request->get('STATUS')->willReturn(95);
        $request->get('CARDNO')->willReturn('555xxxxxxxxxxxxxxxx');
        $request->get('PAYID')->willReturn('trx-454564564');
        $request->get('NCERROR')->willReturn(0);
        $request->get('BRAND')->willReturn('CIC');
        $request->get('ED')->willReturn('12-2015');
        $request->get('TRXDATE')->willReturn('22-10-2013');
        $request->get('CN')->willReturn('Php Spec');
        $request->get('SHASIGN')->willReturn('4561g56dfs4g98ds64g3ds8741.s249861');
        $request->get('ECI')->willReturn(null);
        $request->get('COMPLUS')->willReturn(null);
        $request->get('IP')->willReturn('127.0.0.1');
        $request->get('ALIAS')->willReturn(null);

    }

}
