<?php

namespace Ecedi\Donate\CoreBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Ecedi\Donate\CoreBundle\Event\DonateEvents;
use Ecedi\Donate\CoreBundle\Event\DonationRequestedEvent;
use Symfony\Component\DependencyInjection\ContainerAware;

class CampaignTrackerListener extends ContainerAware implements EventSubscriberInterface
{
    private $key;

    public function __construct($queryKey)
    {
        $this->key = $queryKey;

    }

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(DonateEvents::DONATION_REQUESTED => 'donationRequested');
    }

    public function donationRequested(DonationRequestedEvent $e)
    {
        $intent = $e->getIntent();

        $request = $this->container->get('request');

        $gac = $this->container->get('donate_core.analytics.cookieparser');
        $utm = $gac->parseCookies($request->cookies);

        if ($request->query->has($this->key)) {
            $intent->setCampaign($request->query->get($this->key));

        } else {
            $intent->setCampaign($utm->getCampaignName());
        }

        return $intent->getCampaign();
    }
}
