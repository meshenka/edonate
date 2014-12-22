<?php

namespace Ecedi\Donate\PaymentBundle\PaymentMethod\Plugin;

use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\AbstractPaymentMethod;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CheckPromisePaymentMethod extends AbstractPaymentMethod
{
    const ID = 'check_promise';

    public function getId()
    {
        return self::ID;
    }

    public function getName()
    {
        return 'Send a check';
    }

    /**
     * does not support authorisation tunnel
     *
     * @param  Intent $intent [description]
     * @return [type] [description]
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

            //TODO should we dispatch an event or something?
            $em->persist($intent);
            $em->flush();

            return new RedirectResponse($this->router->generate('donate_payment_check_promise_completed'));
        }

        $response = new Response();
        $response->setStatusCode(500);

        return $response;
    }

    public function getTunnel()
    {
        return self::TUNNEL_SPOT;
    }
}
