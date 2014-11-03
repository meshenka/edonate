<?php

namespace Ecedi\Donate\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Ecedi\Donate\PaymentBundle\PaymentMethod\Plugin\CheckPromisePaymentMethod;

use Symfony\Component\HttpFoundation\Response;

class CheckPromiseController extends Controller
{
    /**
     * @Route("/check/completed", name="donate_payment_check_promise_completed")
     * @Template()
     */
    public function payAction()
    {
        //cette route est cache-control: private car elle peut contenir des info sur la transaction
        $session = $this->getRequest()->getSession();
        $intentRepo = $this->getDoctrine()->getRepository('DonateCoreBundle:Intent');

        if ($session->has('intentId')) {
            $intentId = $session->get('intentId');

            return ['intent' => $intentRepo->find($intentId)];
        }

        //en env de dev on peut afficher la page avec un payment OK
        if ($this->container->getParameter('kernel.environment') === 'dev') {

           $intent = $intentRepo->findOneBy(['status' => Intent::STATUS_DONE, 'paymentMethod' => CheckPromisePaymentMethod::ID]);
           if ($intent) {
            return ['intent' => $intent];
           }

        }
        //gerer par une 404 l'accès à la page sans sessions
        //else this Intent is already managed, or not in session
        $response = new Response();
        $response->setStatusCode(417);

        return $response;
    }
}
