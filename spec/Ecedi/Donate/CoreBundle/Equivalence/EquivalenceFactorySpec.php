<?php

namespace spec\Ecedi\Donate\CoreBundle\Equivalence;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EquivalenceFactorySpec extends ObjectBehavior
{
	function let() {
		$this->beConstructedWith([['amount' => 10, 'label'=> '10', 'currency' => 'USD'], ['amount' => 20, 'label'=> '20', 'currency' => 'EUR']]);

	}
    function it_is_initializable()
    {
        $this->shouldHaveType('Ecedi\Donate\CoreBundle\Equivalence\EquivalenceFactory');
    }

    function it_should_create_equivalence() {
    	$amount = 10;
    	$label = 'php spec test';
    	$currency = 'EUR';

    	$eq = $this->create($amount, $label, $currency);
    	$eq->shouldReturnAnInstanceOf('Ecedi\Donate\CoreBundle\Entity\Equivalence');
    }

    function it_should_get_equivalence_from_configuration() {
    	$get = $this->get();
    	$get->shouldBeArray();
    	$get->shouldHaveCount(2);
        $get[0]->getAmount()->shouldReturn(10);
    }
}
