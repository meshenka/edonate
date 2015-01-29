<?php

namespace Ecedi\Donate\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    /**
     * @Route("/" , name="donate_admin_dashboard")
     */
    public function indexAction()
    {
        return $this->render('DonateAdminBundle:Dashboard:index.html.twig');
    }

    /**
     */
    public function lastIntentsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $lastIntents = $em->getRepository('DonateCoreBundle:Intent')->getLastIntentsByLimit(5);

        return $this->render('DonateAdminBundle:Dashboard:lastIntents.html.twig', [
            'id'            => 'last-intents',
            'title'         => $this->get('translator')->trans('Last donations'),
            'lastIntents'   => $lastIntents,
        ]);
    }

    /**
     */
    public function errorIntentsAction()
    {
        $intents = $this->getDoctrine()->getManager()->getRepository('DonateCoreBundle:Intent')->findBy(
            ['status' => Intent::STATUS_ERROR],
            ['createdAt' => 'DESC'], 5, 0);

        return $this->render('DonateAdminBundle:Dashboard:errorIntents.html.twig', [
            'id'        => 'error-intents',
            'title'     => $this->get('translator')->trans('Last donations in error'),
            'intents'   => $intents,
        ]);
    }

    /**
     * En cache pour 1h
     */
    public function statsIntentsAction()
    {
        $response = new Response();
        $response->setPublic();
        $response->setSharedMaxAge(3600);

        $ir = $this->getDoctrine()->getManager()->getRepository('DonateCoreBundle:Intent');

        $stats = [];
        foreach (Intent::getPossibleStatus() as $status) {
            $stats[$status] = $ir->getCountByStatus($status);
        }

        return $this->render('DonateAdminBundle:Dashboard:statsIntents.html.twig', [
            'id'        => 'stats-intents',
            'title'     => $this->get('translator')->trans('Donations distributions'), //'Répartition des dons',
            'stats'     => $stats,
        ], $response);
    }

    /**
     */
    public function statsGaAction()
    {
        return $this->render('DonateAdminBundle:Dashboard:statsGa.html.twig', [
            'id'        => 'stats-ga',
            'title'     => $this->get('translator')->trans('Visits'), //'Fréquentation',
            'apiKey'    => $this->container->getParameter('donate_admin.analytics.api_key'),
            'dataIds'   => $this->container->getParameter('donate_admin.analytics.data_ids'),
            'clientId'  => $this->container->getParameter('donate_admin.analytics.client_id'),
        ]);
    }

    /**
     */
    public function highestSpotIntentsAction()
    {
        $results = $this->getDoctrine()->getManager()->getRepository('DonateCoreBundle:Intent')->getHighestSpotDonatorIntent(5);

        return $this->render('DonateAdminBundle:Dashboard:highestSpotIntents.html.twig', [
            'id'        => 'highest-spot-intents',
            'title'     => $this->get('translator')->trans('Top five spot donators'), //'TOP FIVE des donateurs ponctuels',
            'results'   => $results,
        ]);
    }

    /**
     */
    public function highestRecurringIntentsAction()
    {
        $results = $this->getDoctrine()->getManager()->getRepository('DonateCoreBundle:Intent')->getHighestRecurringDonatorIntent(5);

        return $this->render('DonateAdminBundle:Dashboard:highestRecurringIntents.html.twig', [
            'id'        => 'highest-recurrin-intents',
            'title'     => $this->get('translator')->trans('Top five recurring donators'), //'TOP FIVE des donateurs réguliers'
            'results'   => $results,
        ]);
    }

    /**
     *
     */
    public function bestDonatorsAction()
    {
        $results = $this->getDoctrine()->getManager()->getRepository('DonateCoreBundle:Payment')->getHighestDonatorPayment(5);

        return $this->render('DonateAdminBundle:Dashboard:bestDonators.html.twig', [
            'id'        => 'best-donators',
            'title'     => $this->get('translator')->trans('Top five donators'), //'TOP FIVE des plus gros donateurs'
            'results'   => $results,
        ]);
    }
}
