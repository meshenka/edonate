<?php

namespace spec\Ecedi\Donate\OgoneBundle\Ogone;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;

class ResponseSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Ecedi\Donate\OgoneBundle\Ogone\Response');
    }

    public function it_is_initializable_via_request(Request $request)
    {
        $this->trainRequest($request);

        $response = $this->createFromRequest($request);
        $response->shouldHaveType('Ecedi\Donate\OgoneBundle\Ogone\Response');
    }

    protected function trainRequest(Request $request)
    {
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
