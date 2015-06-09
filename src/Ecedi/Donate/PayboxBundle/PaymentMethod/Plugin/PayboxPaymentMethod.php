<?php
/**
 * @author Alexandre Fayolle <afayolle@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @package eDonate
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Ecedi\Donate\PayboxBundle\PaymentMethod\Plugin;

use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\AbstractPaymentMethod;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * A payment plugin for eCollect that plug Paybox TPE for Direct Payment
 * {@inheritdoc}
 * @since  2.2.0
 */
class PayboxPaymentMethod extends AbstractPaymentMethod
{
    public function getId()
    {
        return 'paybox';
    }

    public function getName()
    {
        return 'Paybox';
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
            $intent->setStatus(Intent::STATUS_PENDING);

            $entityMgr = $this->doctine->getManager();
            $entityMgr->persist($intent);
            $entityMgr->flush();

            return new RedirectResponse($this->router->generate('donate_paybox_pay'));
        }

        return new Response('', 500);
    }

    public function getTunnel()
    {
        return self::TUNNEL_SPOT;
    }
}
