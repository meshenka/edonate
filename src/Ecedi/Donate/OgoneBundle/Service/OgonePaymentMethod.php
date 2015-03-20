<?php
/**
 * @author  Sylvain Gogel <sgogel@ecedi.fr>
 * @package ECollecte
 * @subpackage PaymentMethod
 */
namespace Ecedi\Donate\OgoneBundle\Service;

use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\AbstractPaymentMethod;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * A payment plugin for eCollect that plug DirectPayment with online/off-site TPE
 * @since  1.0.0
 */
class OgonePaymentMethod extends AbstractPaymentMethod
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
            $intent->setStatus(Intent::STATUS_PENDING);

            $entityMgr = $this->doctine->getManager();
            $entityMgr->persist($intent);
            $entityMgr->flush();

            return new RedirectResponse($this->router->generate('donate_ogone_pay'));
        }

        return new Response('', 500);
    }

    public function getTunnel()
    {
        return self::TUNNEL_SPOT;
    }
}
