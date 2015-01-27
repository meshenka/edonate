<?php

namespace spec\Ecedi\Donate\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use Ecedi\Donate\CoreBundle\Event\DonationRequestedEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Ecedi\Donate\CoreBundle\Analytics\GoogleCookieParser;
class CampaignTrackerListenerSpec extends ObjectBehavior
{
    private $container;
    private $request;

    public function let(Request $request, ContainerInterface $container, GoogleCookieParser $parser)
    {
        $this->trainRequest($request);
        $container->get('request')->willReturn($request);
        $container->get('donate_core.analytics.cookieparser')->willReturn($parser);

        $this->container = $container;
        $this->request = $request;
        $this->beConstructedWith('_utm');
        $this->setContainer($container);
    }

    private function trainRequest(Request $request, $utm = '_utm')
    {
        //train Query
        $request->query = new ParameterBag();
        $request->query->set($utm, 'spec');
        $request->cookies = new ParameterBag();
    }

    public function it_is_initializable()
    {
        // $this->beConstructedWith('_utm');
        $this->shouldHaveType('Ecedi\Donate\CoreBundle\EventListener\CampaignTrackerListener');
        $this->shouldHaveType('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    public function it_should_listen_to_new_intent_event()
    {
        $this->beConstructedWith('_utm');
        $this->getSubscribedEvents()->shouldBeArray();
    }

    public function it_should_add_campaign_to_intent(DonationRequestedEvent $ev)
    {
        $intent = new Intent('100', 'phpspec');
        $ev->getIntent()->willReturn($intent);

        $this->donationRequested($ev)->shouldReturn('spec');
    }

    public function it_should_not_add_campaign_when_request_query_does_not_match(DonationRequestedEvent $ev)
    {
        // TODO fix this test
        // $this->trainRequest($this->request, '_ko');

        // $intent = new Intent('100', 'phpspec');
        // $ev->getIntent()->willReturn($intent);

        // $this->donationRequested($ev)->shouldBeNull();
    }
}
