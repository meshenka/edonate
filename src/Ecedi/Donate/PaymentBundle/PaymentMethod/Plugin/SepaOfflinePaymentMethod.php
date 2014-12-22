<?php
/**
 * @author  Sylvain Gogel <sgogel@ecedi.fr>
 * @package ECollecte
 * @subpackage PaymentMethod
 */
namespace Ecedi\Donate\PaymentBundle\PaymentMethod\Plugin;

use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\AbstractPaymentMethod;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * an offline payment method that allow user to print a mandat to send by postal mail
 * to the association
 *
 * We call it offline to mark it different than online SEPA Mandate with direct numeric signature
 *
 * @since  2.0.0
 */
class SepaOfflinePaymentMethod extends AbstractPaymentMethod
{
    const ID = 'sepa_offline';

    public function getId()
    {
        return self::ID;
    }

    public function getName()
    {
        return 'Send a SEPA Mandate';
    }

    /**
     * We use the autorize tunnel as it is for a recurring payment
     *
     * payment won't be tracked
     *
     * @param  Intent $intent [description]
     * @return [type] [description]
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
     * @return [type] [description]
     */
    public function pay(Intent $intent)
    {
        return false;
    }

    public function getTunnel()
    {
        return self::TUNNEL_RECURING;
    }
}
