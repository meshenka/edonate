<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @package Ecollecte
 */

namespace Ecedi\Donate\OgoneBundle\EventListener;

use Ecedi\Donate\OgoneBundle\Ogone\PostSale\PostSaleManager;
use Psr\Log\LoggerInterface;
use Ecedi\Donate\OgoneBundle\OgoneEvents;
use Ecedi\Donate\OgoneBundle\Event\PostSaleEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
/**
 * this listener manage post sale and generate Payments
 * @since  2.2.0 listen to OgoneEvents::POSTSALE and receive a
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

    public function onPostSale(PostSaleEvent $event)
    {
        $this->logger->debug('before postsale manager');
        $payment = $this->manager->handle($event->getResponse());
        $event->setPayment($payment);
    }

    public static function getSubscribedEvents()
    {
        return array(OgoneEvents::POSTSALE => array(
                array('onPostSale', 10),
            ),
        );
    }
}
