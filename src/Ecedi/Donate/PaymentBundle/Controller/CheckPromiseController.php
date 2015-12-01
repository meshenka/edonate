<?php
/**
 * @author  Sylvain Gogel <sgogel@ecedi.fr>
 * @package eDonate
 * @subpackage Check
 * @copyright Agence Ecedi 2014
 */
namespace Ecedi\Donate\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Ecedi\Donate\PaymentBundle\PaymentMethod\Plugin\CheckPromisePaymentMethod;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class CheckPromiseController extends Controller
{
    /**
     * @Route("/check/completed", name="donate_payment_check_promise_completed")
     */
    public function payAction(Request $request)
    {
        //cette route est cache-control: private car elle peut contenir des info sur la transaction
        $session = $request->getSession();
        $intentRepo = $this->getDoctrine()->getRepository('DonateCoreBundle:Intent');

        if ($session->has('intentId')) {
            $intentId = $session->get('intentId');

            return $this->render('DonatePaymentBundle:CheckPromise:pay.html.twig', [
                'intent' => $intentRepo->find($intentId)
            ]);
        }

        //en env de dev on peut afficher la page avec un payment OK
        if ($this->getParameter('kernel.environment') === 'dev') {
            $intent = $intentRepo->findOneBy(['status' => Intent::STATUS_DONE, 'paymentMethod' => CheckPromisePaymentMethod::ID]);
            if ($intent) {
                return $this->render('DonatePaymentBundle:CheckPromise:pay.html.twig', [
                    'intent' => $intent
                ]);
            }
        }
        //gerer par une 404 l'accès à la page sans sessions
        //else this Intent is already managed, or not in session
        $response = new Response();
        $response->setStatusCode(417);

        return $response;
    }
}
