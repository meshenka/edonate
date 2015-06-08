<?php
/**
 * @author Alexandre Fayolle <afayolle@ecedi.fr>
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @package eDonate
 */

namespace Ecedi\Donate\PayboxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Symfony\Component\HttpFoundation\Response;

/**
 * this controller display payment redirect form
 * @since  2.2.0
 */
class PayboxController extends Controller
{
    /**
     * @Route("/pay",  name="donate_paybox_pay")
     */
    public function payAction()
    {
        $session = $this->getRequest()->getSession();

        $intentRepo = $this->getDoctrine()->getRepository('DonateCoreBundle:Intent');

        if ($session->get('intentId')) {
            $intent = $intentRepo->find($session->get('intentId'));

            if ($intent->getStatus() == Intent::STATUS_NEW || $intent->getStatus() == Intent::STATUS_PENDING) {
                $paybox = $this->get('lexik_paybox.request_handler');
                //@TODO rendre le PBX_CMD configurable et adapter le code de l'IpnData

                $paybox->setParameters(array(
                    'PBX_CMD'          => 'DON-'.$intent->getId(),
                    'PBX_DEVISE'       => '978',
                    'PBX_PORTEUR'      => $intent->getCustomer()->getEmail(),
                    'PBX_RETOUR'       => 'M:M;R:R;A:A;E:E;K:K;T:T;F:F;W:W;S:S;D:D;Q:Q;Y:Y',
                    'PBX_TOTAL'        => $intent->getAmount(),
                    'PBX_TYPEPAIEMENT' => 'CARTE',
                    'PBX_TYPECARTE'    => 'CB',
                    'PBX_EFFECTUE'     => $this->generateUrl('donate_front_completed', [], true),
                    'PBX_REFUSE'       => $this->generateUrl('donate_front_denied', [], true),
                    'PBX_ANNULE'       => $this->generateUrl('donate_front_canceled', [], true),
                    'PBX_RUF1'         => 'POST',
                    'PBX_REPONDRE_A'   => $this->generateUrl('lexik_paybox_ipn', ['time' => time()], true),
                ));

                return $this->render('DonatePayboxBundle:Paybox:pay.html.twig', [
                    'intent'    => $intent,
                    'url'       => $paybox->getUrl(),
                    'form'      => $paybox->getForm()->createView(),
                ]);
            }
        }

        return new Response('', 403);
    }
}
