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

            //  return $this->container->get('templating')->renderResponse($view, $parameters, $response);
            return $this->templating->renderResponse('DonatePaymentBundle:CheckPromise:pay.html.twig', array(
                'intent' => $intent));

        } else {
            $response = new Response();
            $response->setStatusCode(500);

            return $response;
        }
    }
	
}