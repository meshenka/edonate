<?php
/**
 * @author  Sylvain Gogel <sgogel@ecedi.fr>
 * @package Ecollecte
 * @subpackage SEPA
 * @copyright Agence Ecedi 2014
 */
namespace Ecedi\Donate\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Ecedi\Donate\PaymentBundle\PaymentMethod\Plugin\SepaOfflinePaymentMethod;
use Symfony\Component\HttpFoundation\Response;
use Ecedi\Donate\PaymentBundle\Event\IntentDocumentGeneratedEvent;
use Ecedi\Donate\PaymentBundle\Event\PaymentEvents;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Controller for display of SEPA Offline return pages
 * @since  2.0.0
 */
class SepaOfflineController extends Controller
{
    /**
     * @Route("/sepa-offline/completed", name="donate_payment_sepa_offline_completed")
     * @since  2.0.0
     * @return mixed array of twig variables, or a manual Symfony\Component\HttpFoundation\Response
     */
    public function autorizeAction()
    {
        //cette route est cache-control: private car elle peut contenir des info sur la transaction
        $session = $this->getRequest()->getSession();
        $intentRepo = $this->getDoctrine()->getRepository('DonateCoreBundle:Intent');

        if ($session->has('intentId')) {
            $intentId = $session->get('intentId');

            return $this->render('DonatePaymentBundle:SepaOffline:autorize.html.twig', [
                'intent' => $intentRepo->find($intentId)
            ]);
        }

        //en env de dev on peut afficher la page avec un payment OK
        if ($this->container->getParameter('kernel.environment') === 'dev') {
            $intent = $intentRepo->findOneBy(['status' => Intent::STATUS_DONE, 'paymentMethod' => SepaOfflinePaymentMethod::ID]);
            if ($intent) {
                return $this->render('DonatePaymentBundle:SepaOffline:autorize.html.twig', [
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

    /**
     *
     * This route generate a PDF version of the SEPA Mandate
     * To make it work you must implement a listener on PaymentEvents::INTENT_DOCUMENT_GENERATED and produce a ZendPdf\PdfDocument instance
     * in your own bundle (do not hack the core)
     * the listener should use a RumGeneratorInterface.
     * 2 services are enabled by default, and you can create your own
     *   * donate_payment.sepa_offline.rum.empty
     *   * donate_payment.sepa_offline.rum.preformated
     *
     *
     * @Route("/sepa-offline/mandate/pdf", name="donate_payment_sepa_offline_document")
     * @since  2.0.0
     * @todo  Faire un lien avec un token unique ?
     * @see  http://symfony.com/fr/doc/current/components/http_foundation/introduction.html#retourner-des-fichiers
     * @return Symfony\Component\HttpFoundation\Response an HTTP response with the file, or an HTTP 403 Response
     */
    public function generatePdf()
    {
        $session = $this->getRequest()->getSession();
        $intentRepo = $this->getDoctrine()->getRepository('DonateCoreBundle:Intent');

        if ($session->has('intentId')) {
            $intentId = $session->get('intentId');
            if ($intent = $intentRepo->find($intentId)) {
                $event = new IntentDocumentGeneratedEvent($intent);
                $this->get('event_dispatcher')->dispatch(PaymentEvents::INTENT_DOCUMENT_GENERATED,  $event);

                if ($document = $event->getDocument()) {
                    $response = new Response($document->render());

                    $response->headers->set('Content-Type', 'application/pdf');
                    $disposition = $response->headers->makeDisposition(
                        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                        'sepa-'.$intentId.'.pdf'
                    );

                    $response->headers->set('Content-Disposition', $disposition);

                    return $response;
                }
            }
        }

        $response = new Response();
        $response->setStatusCode(403);

        return $response;
    }
}
