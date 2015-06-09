<?php
/**
 * @author Alexandre Fayolle <alf@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @package eDonate
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Ecedi\Donate\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Patch;
use Ecedi\Donate\CoreBundle\Entity\Payment;
//use Ecedi\Donate\CoreBundle\Form\PaymentType;

/**
* @NamePrefix("donate_api_v1_")
*
* L' annotation ...View(serializerGroups={"REST"}) utilisée ci-dessous permet de retourner
* seulement les éléments de l'entité qui appartiennent au groupe "REST" (défini dans l'entité)
* cf: Payment Entity et l'annotation ...Groups({"REST"})
*/
class PaymentsController extends Controller
{
    /**
    * @View(serializerGroups={"REST"})
    * @return array
    */
    public function getPaymentsAction()
    {
        $request = Request::createFromGlobals();
        $restParams = $request->query->All();   // On récupère tous les paramètres passés en GET

        $em = $this->getDoctrine()->getManager();
        $paymentRepo = $em->getRepository('DonateCoreBundle:Payment');

        $payments = $paymentRepo->findByRestParams($restParams);
        $nbResults = $paymentRepo->countAll();

        return [
            'nbResults' => $nbResults,
            'payments' => $payments
        ];
    }

    /**
    * @View(serializerGroups={"REST"})
    * @param int $paymentId
    * @return Payment
    */
    public function getPaymentAction($paymentId)
    {
        $em = $this->getDoctrine()->getManager();
        $payment = $em->getRepository('DonateCoreBundle:Payment')->find($paymentId);

        $this->throwNotFoundExceptionIfNotPayment($payment);  // Contrôle sur l'existence de l'entité

        return [
            'payment' => $payment
        ];
    }

    /**
     * @View(statusCode=204) -- "No content" - Retourné quand l'update de l'entité a été réalisé
     * @param int $paymentId
     */
    public function patchPaymentAction($paymentId)
    {
        $em = $this->getDoctrine()->getManager();
        $payment = $em->getRepository('DonateCoreBundle:Payment')->find($paymentId);

        $this->throwNotFoundExceptionIfNotPayment($payment);  // Contrôle sur l'existence de l'entité

        return $this->processForm($payment, 'PATCH');
    }

    /**
    * Processing the Payment form
    *
    * @param Payment $payment
    * @param string $method -- la méthode du formulaire pour récupérer les données
    */
    private function processForm(Payment $payment, $method = 'POST')
    {
        $form = $this->createForm(new PaymentType(), $payment, array('method' => $method));
        $form->handleRequest($this->getRequest());

        if ($form->isValid()) { // Si les données sont correctes, on enregistre notre Payment
            $em = $this->getDoctrine()->getManager();
            $em->persist($payment);
            $em->flush();
        }
    }

    /**
    * Envoie d'une Exception (404 -- Not Found) si l'on ne retrouve pas le Payment
    *
    * @param $payment
    */
    private function throwNotFoundExceptionIfNotPayment($payment)
    {
        if (!$payment instanceof Payment) {
            throw $this->createNotFoundException("Payment not found, check id or parameters.");
        }
    }
}
