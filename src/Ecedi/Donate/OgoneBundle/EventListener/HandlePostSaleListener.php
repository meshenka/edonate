<?php

namespace Ecedi\Donate\OgoneBundle\EventListener;

use Ecedi\Donate\OgoneBundle\Ogone\PostSale\PostSaleManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerInterface;
use Ecedi\Donate\CoreBundle\Event\PaymentReceivedEvent;
use Ecedi\Donate\OgoneBundle\OgoneEvents;

/**
 * Sends emails for the memory spool.
 *
 * Emails are sent on the kernel.terminate event.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * TODO attention avec les evenements il faut les traiter que quand cela est pertinant
 */
class HandlePostSaleListener implements EventSubscriberInterface
{
    private $manager;
    private $logger;

    public function __construct(PostSaleManager $manager, LoggerInterface $logger)
    {
        $this->manager = $manager;
        $this->logger = $logger;
    }

    public function onPostSale(PaymentReceivedEvent $event)
    {
        $this->logger->debug('before postsale manager');
        $this->manager->handle($event->getResponse());
    }

    public static function getSubscribedEvents()
    {
        return array(OgoneEvents::POSTSALE => array(
                array('onPostSale', 10),
            ),
        );
    }
}
