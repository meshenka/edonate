<?php

namespace spec\Ecedi\Donate\CoreBundle\PaymentMethod;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Symfony\Component\Translation\TranslatorInterface;
use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\PaymentMethodInterface;

class DiscoverySpec extends ObjectBehavior
{
	const PM_NAME = 'phpspec payment method';

	function let(TranslatorInterface $translator) {

		$translator->trans(DiscoverySpec::PM_NAME)->willReturn(DiscoverySpec::PM_NAME);
    	$this->beConstructedWith($translator);    	
	}

    function it_is_initializable()
    {

        $this->shouldHaveType('Ecedi\Donate\CoreBundle\PaymentMethod\Discovery');
    }

    function it_should_add_payment_methods(PaymentMethodInterface $pm) {

    	
    	$this->getAvailableMethods()->shouldHaveCount(0);

    	$this->trainPaymentMethod($pm);
    	//$this->getTranslator()->trans(DiscoverySpec::PM_NAME)->willReturn(DiscoverySpec::PM_NAME);
    	$this->addMethod($pm);

    	$result = $this->getAvailableMethods();
    	$result->shouldHaveKey('spec');
    }

    protected function trainPaymentMethod(PaymentMethodInterface $pm) {
    	$pm->getId()->willReturn('spec');
    	$pm->getName()->willReturn(DiscoverySpec::PM_NAME);

    }

    function it_should_find_payment_methods_by_id(PaymentMethodInterface $pm) {
		$this->trainPaymentMethod($pm);
		$this->addMethod($pm);

		$this->getMethod('spec')->shouldReturn($pm);
    	
    }

}
