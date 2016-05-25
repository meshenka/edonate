<?php

namespace Ecedi\Donate\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\Response;
use Ecedi\Donate\CoreBundle\Entity\Payment;
use Symfony\Component\HttpFoundation\Request;

class ReturnController extends Controller
{
    /**
     * @Route("/{_locale}/completed", name="donate_front_completed", defaults={"_locale"="fr"}, requirements = {"_locale" = "fr|en"})
     */
    public function completedAction(Request $request)
    {
        //cette route est cache-control: private car elle peut contenur des info sur la transaction
         $session = $request->getSession();

        if ($session->has('intentId')) {
            $intentId = $session->get('intentId');
            $intentRepo = $this->getDoctrine()->getRepository('DonateCoreBundle:Intent');

            return $this->render(':front/return:completed.html.twig', [
                'intent' => $intentRepo->find($intentId)
            ]);
        }

        //en env de dev on peut afficher la page avec un payment OK
        if ($this->getParameter('kernel.environment') === 'dev') {
            $paymentRepo = $this->getDoctrine()->getRepository('DonateCoreBundle:Payment');

            $payment = $paymentRepo->findOneBy(array('status' => Payment::STATUS_PAYED));
            if ($payment) {
                return $this->render(':front/return:completed.html.twig', [
                    'intent' => $payment->getIntent()
                ]);
            }
        }

        //gerer par une 404 l'accès à la page sans sessions
        //else this Intent is already managed, or not in session
        $response = new Response();
        $response->setStatusCode(417);

        return $response;
    }

    /**
     * @Route("/{_locale}/canceled", name="donate_front_canceled", defaults={"_locale"="fr"}, requirements = {"_locale" = "fr|en"})
     * @Cache(public="true", maxage="3600", smaxage="3600")
     */
    public function canceledAction()
    {
        return $this->render(':front/return:canceled.html.twig');
    }

    /**
     * @Route("/{_locale}/denied", name="donate_front_denied", defaults={"_locale"="fr"}, requirements = {"_locale" = "fr|en"})
     * @Cache(public="true", maxage="3600", smaxage="3600")
     */
    public function deniedAction()
    {
        return $this->render(':front/return:denied.html.twig');
    }

    /**
     * @Route("/{_locale}/failed", name="donate_front_failed", defaults={"_locale"="fr"}, requirements = {"_locale" = "fr|en"})
     * @Cache(public="true", maxage="3600", smaxage="3600")
     */
    public function failedAction()
    {
        return $this->render(':front/return:failed.html.twig');
    }
}
