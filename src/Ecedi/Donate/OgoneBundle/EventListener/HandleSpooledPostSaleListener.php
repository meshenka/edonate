<?php

namespace Ecedi\Donate\OgoneBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Ecedi\Donate\OgoneBundle\Ogone\PostSaleManager;
use Symfony\Component\Console\ConsoleEvents;

/**
 * Sends emails for the memory spool.
 *
 * Emails are sent on the kernel.terminate event.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class HandleSpooledPostSaleListener implements EventSubscriberInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onTerminate()
    {
        $logger  = $this->container->get('logger');
        $logger->debug('we now handle spooled post sales');

        if (!$this->container->has('donate_ogone.postsale.manager')) {
            $logger->error('no postsale manager');
            return;
        }

        $postSaleManager = $this->container->get('donate_ogone.postsale.manager');
         $logger->debug(get_class($postSaleManager));
        if ($postSaleManager instanceof \Ecedi\Donate\OgoneBundle\Ogone\PostSale\MemorySpoolPostSaleManager) {
            $logger->debug('flush!!');
            $postSaleManager->flush();
        }

    }

    public static function getSubscribedEvents()
    {
        $listeners = [
            KernelEvents::TERMINATE => ['onTerminate', 10],
            ConsoleEvents::TERMINATE => ['onTerminate', 10]
        ];

        return $listeners;
    }
}
