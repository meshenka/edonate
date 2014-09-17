<?php

namespace Ecedi\Donate\OgoneBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Ecedi\Donate\CoreBundle\Entity\Payment;
use Ecedi\Donate\OgoneBundle\Ogone\Response as OgoneResponse;

/**
 * autoload the json serialized response as a Ecedi\Donate\OgoneBundle\Ogone\Response instance, injected in the entity
 * TODO attention avec les evenements il faut les traiter que quand cela est pertinant
 */
class PostSaleResponseLoaderListener
{
	public function postLoad(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();
        $entityManager = $event->getEntityManager();

        if ($entity instanceof Payment) {
        	if($entity->getResponse()) {
				$r = OgoneResponse::createFromArray($entity->getResponse());
	            $entity->setResponse($r);
	        }
        }
    }
}