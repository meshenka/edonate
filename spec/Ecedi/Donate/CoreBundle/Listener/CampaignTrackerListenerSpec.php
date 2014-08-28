<?php

namespace spec\Ecedi\Donate\CoreBundle\Listener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Ecedi\Donate\CoreBundle\Event\DonateEvents;
use Ecedi\Donate\CoreBundle\Event\IntentEvent;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

class CampaignTrackerListenerSpec extends ObjectBehavior
{

	private function trainRequest(Request $request, $utm = '_utm') {
		//train Query
		$request->query = new ParameterBag();
		$request->query->set($utm,'spec');

	}

	
    function it_is_initializable()
    {
    	$this->beConstructedWith('_utm');

        $this->shouldHaveType('Ecedi\Donate\CoreBundle\Listener\CampaignTrackerListener');
        $this->shouldHaveType('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_should_listen_to_new_intent_event()
    {
		$this->beConstructedWith('_utm');    	
    	$this->getSubscribedEvents()->shouldBeArray();
    }

    function it_should_add_campaign_to_intent(IntentEvent $ev) {

	   //$this->trainRequest($request);		
		$this->beConstructedWith('_utm');

		$intent = new Intent('100', 'phpspec');
		$ev->getIntent()->willReturn($intent);

    	$this->postNewIntent($ev)->shouldReturn('spec');

    }

    function it_should_not_add_campaign_when_request_query_does_not_match(IntentEvent $ev) {
		//$this->trainRequest($request, '_ko');		
		$this->beConstructedWith( '_utm');

		$intent = new Intent('100', 'phpspec');

    	$this->postNewIntent($ev)->shouldBeNull();

    }


}
