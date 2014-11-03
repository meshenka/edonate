<?php

namespace Ecedi\Donate\PaymentBundle\PaymentMethod\Plugin;

use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\PaymentMethodInterface;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * an offline payment method that allow user to print a mandat to send by postal mail
 * to the association
 *
 * We call it offline to mark it different than online SEPA Mandate with direct numeric signature
 *
 * TODO premier payment méthod pour le tunnel de vente recurring à voir comment le traiter au niveua IntentManager!!
 * 
 */
class SepaOfflinePaymentMethod implements PaymentMethodInterface {

	private $templating;
	private $doctrine;
    private $router;

    const ID = 'sepa_offline';

    public function getId()
    {
        return self::ID;
    }

    public function getName()
    {
        return 'Send a SEPA Mandate';
    }

    public function __construct(RegistryInterface $doctrine, EngineInterface $templating, RouterInterface $router) {
    	$this->templating = $templating;
    	$this->doctrine = $doctrine;
        $this->router = $router;
    }

    /**
     * We use the autorize tunnel as it is for a recurring payment
     *
     * payment won't be tracked
     * 
     * @param  Intent $intent [description]
     * @return [type]         [description]
     */
    public function autorize(Intent $intent)
    {
        if ($intent->getStatus() === Intent::STATUS_NEW) {

        	//le payement est immédiatement terminé,
        	$intent->setStatus(Intent::STATUS_DONE);
            $intent->setType(Intent::TYPE_RECURING);
        	$em = $this->doctrine->getManager();

            //TODO should we dispatch an event or something?
        	$em->persist($intent);
        	$em->flush();
            
            return new RedirectResponse($this->router->generate('donate_payment_sepa_offline_completed'));

        }

        $response = new Response();
        $response->setStatusCode(500);

        return $response;        
    }
    /**
     * does not support direct payment
     * 
     * @param  Intent $intent [description]
     * @return [type]         [description]
     */
    public function pay(Intent $intent)
    {
        return false;
    }
	
    public function getTunnel() {
        return self::TUNNEL_RECURING;
    }

}