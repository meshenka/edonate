<?php

namespace Ecedi\Donate\OgoneBundle\Service;

use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\PaymentMethodInterface;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OgonePaymentMethod extends Controller implements PaymentMethodInterface
{

    public function getId()
    {
        return 'ogone';
    }

    public function getName()
    {
        return 'Ogone';
    }

    /**
     * @TODO implement me
     */
    public function autorize(Intent $intent)
    {
        $response = new Response();
        $response->setStatusCode(500);

        return $response;
    }

    /**
     * return anything that can be managed as a response
     */
    public function pay(Intent $intent)
    {
        if ($intent->getStatus() === Intent::STATUS_NEW) {

            //store intentId in session
            //TODO put this code in CoreBundle
            $request = $this->getRequest();
            $session = $request->getSession();
            $session->set('intentId', $intent->getId());

            // try to see if the locale has been set as a _locale routing parameter
            if ($locale = $request->getLocale()) {
                $session->set('_locale', $locale);
            }

            //ladybug_dump($httpRequest);
            return $this->redirect($this->generateUrl('donate_ogone_pay', []), 301);

        } else {
            $response = new Response();
            $response->setStatusCode(500);

            return $response;
        }
    }

}
