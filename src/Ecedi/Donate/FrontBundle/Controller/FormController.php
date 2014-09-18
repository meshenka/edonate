<?php

namespace Ecedi\Donate\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ecedi\Donate\CoreBundle\Entity\Customer;
use Ecedi\Donate\CoreBundle\Entity\Intent;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

class FormController extends Controller
{

    /**
     * @Route("/{_locale}", name="donate_front_home", defaults={"_locale"="fr"}, requirements = {"_locale" = "fr|en"})
     */
    public function indexAction(Request $request, $_locale)
    {
        //cache validation tjrs public, c'est l'ESI qui gère la sidebar
        $response = new Response();
        // $response->setPublic();
        // $response->setSharedMaxAge(3600);

        $data = new Customer();

        $form = $this->createForm('donate', $data);


        $form->handleRequest($request);
        if ($form->isValid()) {

            $im = $this->get('donate_core.intent_manager');

            //calcul du montant
            $amount = 0;
            if ($form['amount_preselected']->getData() === 'manual' ) {
                $amount = $form['amount_manual']->getData() * 100;
            } else {
                $amount = $form['amount_preselected']->getData() * 100;
            }

            $intent = $im->newIntent($amount, $form['payment_method']->getData());
            $intent->setFiscalReceipt($form['erf']->getData());

            $data->addIntent($intent);

            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            // TODO we should use handleAutorize when using recurring tunnel
            return  $im->handlePay($intent);
        }

        return $this->render('DonateFrontBundle:Form:index.html.twig', array(
            'form' => $form->createView(),
            ), $response);
    }

}
