<?php

namespace Ecedi\Donate\PaymentBundle\PaymentMethod\Plugin;

use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\PaymentMethodInterface;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CheckPromisePaymentMethod implements PaymentMethodInterface {

	private $templating;
	private $doctrine;

    public function getId()
    {
        return 'check_promise';
    }

    public function getName()
    {
        return 'Send a check';
    }

    public function __construct(RegistryInterface $doctrine, EngineInterface $templating) {
    	$this->templating = $templating;
    	$this->doctrine = $doctrine;
    }
    /**
     * does not support authorisation tunnel
     * 
     * @param  Intent $intent [description]
     * @return [type]         [description]
     */
    public function autorize(Intent $intent)
    {
        return false;
    }

    public function pay(Intent $intent)
    {
        if ($intent->getStatus() === Intent::STATUS_NEW) {

        	//le payement est immédiatement terminé,
        	$intent->setStatus(Intent::STATUS_DONE);
        	$em = $this->doctrine->getManager();
        	$em->persist($intent);
        	$em->flush();

            //  j'aime pas trop cette technique parce que sur un F5 dans le navigateur ca refait un don,
            //  je pense qu'il est préferable de faire un redirect vers une url dédié qui ne crée pas 
            //  de nouveaux dons.... p-e en utilisant un token unique dans l'URL ou la session 
            //  pour avoir toujours la même URL ?
            return $this->templating->renderResponse('DonatePaymentBundle:CheckPromise:pay.html.twig', array(
                'intent' => $intent));

        } else {
            $response = new Response();
            $response->setStatusCode(500);

            return $response;
        }
    }
	
}