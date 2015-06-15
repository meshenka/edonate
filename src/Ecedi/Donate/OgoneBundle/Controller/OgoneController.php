<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2014
 * @package eDonate
 */
namespace Ecedi\Donate\OgoneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Ecedi\Donate\CoreBundle\Entity\Payment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Ecedi\Donate\OgoneBundle\Ogone\Response as OgoneResponse;
use Ecedi\Donate\OgoneBundle\OgoneEvents;
use Ecedi\Donate\OgoneBundle\Event\PostSaleEvent;

class OgoneController extends Controller
{
    /**
     * @Route("/pay",  name="donate_ogone_pay")
     */
    public function payAction(Request $request)
    {
        $session = $request->getSession();
        $request->setlocale($session->get('_locale'));

        if ($intentId = $session->get('intentId')) {
            $intentRepository = $this->getDoctrine()->getRepository('DonateCoreBundle:Intent');

            $intent = $intentRepository->find($intentId);

            if ($intent->getStatus() == Intent::STATUS_NEW || $intent->getStatus() == Intent::STATUS_PENDING) {
                $factory = $this->get('donate_ogone.request.factory');

                return $this->render('DonateOgoneBundle:Ogone:pay.html.twig', [
                    'ogone' => $factory->build($intent), 'intent' => $intent
                ]);
            }
        }

        //else this Intent is already managed, or not in session
        return new Response('', 403);
    }

    /**
     * @Route("/api/postsale",  name="donate_ogone_postsale")
     * @since  2.2.0 this router delegate all business logic to PostSaleManager via a OgoneEvents::POSTSALE event
     */
    public function postsaleAction(Request $request)
    {
        $response = OgoneResponse::createFromRequest($request);

        $postSaleEvent =  new PostSaleEvent($response);

        $this->get('event_dispatcher')->dispatch(OgoneEvents::POSTSALE, $postSaleEvent);

        $payment = $postSaleEvent->getPayment();

        //i think this part is optionnal as already done in IntentManager::attachPayment()
        $entityMgr = $this->getDoctrine()->getManager();
        $entityMgr->persist($payment);
        $entityMgr->flush();

        return new JsonResponse(['status' => 'OK']);
    }
}
