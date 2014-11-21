<?php

namespace Ecedi\Donate\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    /**
     * @Route("/" , name="donate_admin_dashboard")
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * @Template()
     */
    public function lastIntentsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $lastIntents = $em->getRepository('DonateCoreBundle:Intent')->getLastIntentsByLimit(5);

        return [
            'id'            => 'last-intents',
            'title'         => $this->get('translator')->trans('Last donations'),
            'lastIntents'   => $lastIntents,
        ];
    }

    /**
     *
     * @Template()
     */
    public function errorIntentsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $intents = $em->getRepository('DonateCoreBundle:Intent')->findBy(
            ['status' => Intent::STATUS_ERROR],
            ['createdAt' => 'DESC'], 5, 0);

        return [
            'id'        => 'error-intents',
            'title'     => $this->get('translator')->trans('Last donations in error'),
            'intents'   => $intents,
        ];
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
     * @Template()
     */
    public function statsGaAction()
    {
        return [
            'id'        => 'stats-ga',
            'title'     => $this->get('translator')->trans('Visits'), //'Fréquentation',
            'apiKey'    => $this->container->getParameter('donate_admin.analytics.api_key'),
            'dataIds'   => $this->container->getParameter('donate_admin.analytics.data_ids'),
            'clientId'  => $this->container->getParameter('donate_admin.analytics.client_id'),
        ];
    }

    /**
     * @Template()
     */
    public function highestSpotIntentsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $results = $em->getRepository('DonateCoreBundle:Intent')->getHighestSpotDonatorIntent(5);

        return [
            'id'        => 'highest-spot-intents',
            'title'     => $this->get('translator')->trans('Top five spot donators'), //'TOP FIVE des donateurs ponctuels',
            'results'   => $results,
        ];
    }

    /**
     * @Template()
     */
    public function highestRecurringIntentsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $results = $em->getRepository('DonateCoreBundle:Intent')->getHighestRecurringDonatorIntent(5);

        return [
            'id'        => 'highest-recurrin-intents',
            'title'     => $this->get('translator')->trans('Top five recurring donators'), //'TOP FIVE des donateurs réguliers'
            'results'   => $results,
        ];
    }

    /**
     *
     * @Template()
     */
    public function bestDonatorsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $results = $em->getRepository('DonateCoreBundle:Payment')->getHighestDonatorPayment(5);

        return [
            'id'        => 'best-donators',
            'title'     => $this->get('translator')->trans('Top five donators'), //'TOP FIVE des plus gros donateurs'
            'results'   => $results,
        ];
    }
}
