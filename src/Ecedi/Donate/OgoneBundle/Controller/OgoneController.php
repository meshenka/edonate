<?php

namespace Ecedi\Donate\OgoneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Ecedi\Donate\CoreBundle\Entity\Payment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Ecedi\Donate\CoreBundle\Event\DonateEvents;
use Ecedi\Donate\CoreBundle\Event\PaymentReceivedEvent;
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
     *
     * TODO solution type BEN
     * Sur la post-sale on ne fait que enregistrer les informations
     * validation/vérification/association à un intent se fera plus tard
     *   - soit via un Handler d'Event DonateEventes:PAYMENT_RECEIVED
     *   - soit via une commande batch (mais du coup il faut savoir si une post-sale a été traité ou non)
     *
     *  Option, ajouter une Entity pour la capture de la réponse Ogone en couplage léger avec Payment ?
     */
    public function postsaleAction(Request $request)
    {
        $response = OgoneResponse::createFromRequest($request);

        $this->get('event_dispatcher')->dispatch(OgoneEvents::POSTSALE,  new PostSaleEvent($response));

        //initialize payment
        $payment = new Payment();
        $payment->setAutorisation($response->getAcceptance()) //n° autorisation
            ->setTransaction($response->getPayId()) //no transaction
            ->setResponseCode($response->getStatus()) //status ogone
            ->setResponse($response);

        $em = $this->getDoctrine()->getManager();

        //si nous somme en mode asynchrone, alors c'est via la commande donate:ogone:postsale que seront envoyés
        // les events
        if ($this->container->getParameter('donate_ogone.async_postsale') == false) {
            $this->get('event_dispatcher')->dispatch(DonateEvents::PAYMENT_RECEIVED,  new PaymentReceivedEvent($payment));
            $this->get('logger')->debug(DonateEvents::PAYMENT_RECEIVED.' dispatched');
        }

        $em->persist($payment);
        $em->flush();

        return new JsonResponse(['status' => 'OK']);
    }
}
