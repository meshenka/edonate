<?php

namespace Ecedi\Donate\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Ecedi\Donate\CoreBundle\Entity\Customer;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Doctrine\ORM\Query;

/**
 * Reporting controller.
 *
 * @Cache(expires="yesterday", public="false")
 */
class ReportingController extends Controller
{
    /**
     * @Route("/intents" , name="donate_admin_reporting_intents")
     * @Template()
     */
    public function intentsAction(Request $request)
    {
        $intentForm = $this->createForm('intent_filters');

        $parameters = $request->query->get('intent_filters');// Récupération des valeures de nos filtres

        if ($parameters) {
            $intentForm->bind($request);// application des filtres sélectionnées au formulaire
        }

        $entityMgr = $this->getDoctrine()->getManager();
        $queryBuilder = $entityMgr->getRepository('DonateCoreBundle:Intent')->getQBIntentsListBy($parameters);

        // gestion de l'export
        if ($intentForm->isValid()) {
            if ($intentForm->get('submit_export')->isClicked()) {
                $exporter = $this->get('ecollect.export.intent');
                $exporter->setExportQb($queryBuilder);
                $content = $exporter->getCsvContent();

                return $this->getCsvResponse($content, 'export_dons', 'ISO-8859-1');
            }
        }

        $pagination = $this->getPagination($request, $queryBuilder->getQuery(), 20);

        return [
            'pagination'    => $pagination,
            'intentForm'    => $intentForm->createView()
        ];
    }

    /**
     * @Route("/intent/{id}/show" , name="donate_admin_reporting_intent_show", defaults={"id" = 0})
     * @Template()
     */
    public function intentShowAction(Request $request, Intent $intent)
    {
        $customerId = $intent->getCustomer()->getId();
        $entityMgr = $this->getDoctrine()->getManager();

        $paymentsQuery = $entityMgr->getRepository('DonateCoreBundle:Payment')->getPaymentsListByIntent(['intentId' => $intent->getId()]);
        $customerOtherIntentsQuery = $entityMgr->getRepository('DonateCoreBundle:Intent')->getIntentsListByCustomer(['customerId' => $customerId], 5, $intent->getId());
        $customerOtherIntents = $customerOtherIntentsQuery->getResult();

        $pagination = $this->getPagination($request, $paymentsQuery, 12);

        return [
            'intent'                => $intent,
            'pagination'            => $pagination,
            'customerOtherIntents'  => $customerOtherIntents,
        ];
    }

    /**
     * @Route("/customers" , name="donate_admin_reporting_customers")
     * @Template()
     */
    public function customersAction(Request $request)
    {
        $customerForm = $this->createForm('customer_filters');

        $parameters = $request->query->get('customer_filters');// Récupération des valeures de nos filtres

        if ($parameters) {
            $customerForm->bind($request);// application des filtres sélectionnées au formulaire
        }

        $entityMgr = $this->getDoctrine()->getManager();
        $query = $entityMgr->getRepository('DonateCoreBundle:Customer')->getCustomersListBy($parameters);

        // gestion de l'export
        if ($customerForm->isValid()) {
            if ($customerForm->get('submit_export')->isClicked()) {
                $exporter = $this->get('ecollect.export.customer');
                $exporter->setExportQuery($query);
                $content = $exporter->getCsvContent();

                return $this->getCsvResponse($content, 'export_donateurs', 'ISO-8859-1');
            }
        }

        $pagination = $this->getPagination($request, $query, 20);

        return [
            'pagination'    => $pagination,
            'customerForm'  => $customerForm->createView()
        ];
    }

    /**
     * @Route("/customer/{id}/show" , name="donate_admin_reporting_customer_show", defaults={"id" = 0})
     * @Template()
     */
    public function customerShowAction(Request $request, Customer $customer)
    {
        $entityMgr = $this->getDoctrine()->getManager();
        $intentsQuery = $entityMgr->getRepository('DonateCoreBundle:Intent')->getIntentsListByCustomer(['customerId' => $customer->getId()]);

        $pagination = $this->getPagination($request, $intentsQuery, 10);

        return [
            'customer'          => $customer,
            'pagination'        => $pagination,
        ];
    }

    /**
     * @Route("/customer/{id}/edit" , name="donate_admin_reporting_customer_edit", defaults={"id" = 0})
     * @Template()
     */
    public function customerEditAction(Request $request, Customer $customer)
    {
        $editForm = $this->createForm('customer', $customer);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entityMgr = $this->getDoctrine()->getManager();

            $entityMgr->persist($customer);
            $entityMgr->flush();
            $noticeMsg = $this->get('translator')->trans("Donator has been updated");
            $this->get('session')->getFlashBag()->set('notice', $noticeMsg);

            return $this->redirect($this->generateUrl('donate_admin_reporting_customer_show', array('id' => $customer->getId())));
        }

        return [
            'editForm'  => $editForm->createView()
        ];
    }

    /**
     * @Template()
     */
    public function customerInfoAction(Customer $customer)
    {
        return [
            'customer'  => $customer
        ];
    }

    /**
    * Fonction pour récupérer notre objet de pagination
    *
    * @param Request $request
    * @param int $limit -- limit du pager
    * @param Query $query -- la requete
    */
    public function getPagination(Request $request, Query $query, $limit = 10)
    {
        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            $limit
        );

        return $pagination;
    }

    /**
    * put your comment there...
    *
    * @param string $content
    * @param string $csvName
    * @param string $charset
    * @return Symfony\Component\HttpFoundation\Response
    */
    public function getCsvResponse($content, $csvName, $charset)
    {
        $response = new Response();
        $response->setContent($content);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv;charset='.$charset);
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$csvName.'_'.date("d_m_Y").'.csv');

        return $response;
    }
}
