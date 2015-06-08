<?php

namespace Ecedi\Donate\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Ecedi\Donate\CoreBundle\Entity\Customer;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Doctrine\ORM\Query;
use Ecedi\Donate\AdminBundle\Form\CustomerType;
use Ecedi\Donate\AdminBundle\Form\CustomerFiltersType;
use Ecedi\Donate\AdminBundle\Form\IntentFiltersType;
/**
 * Reporting controller.
 *
 * @Cache(expires="yesterday", public="false")
 */
class ReportingController extends Controller
{
    /**
     * @Route("/intents" , name="donate_admin_reporting_intents")
     */
    public function intentsAction(Request $request)
    {
        $filters = array();
        $intentForm = $this->createForm(new IntentFiltersType(), $filters, [
            'method' => 'GET',
            ]);

        $intentForm->handleRequest($request);

        $filters = $intentForm->getData();

        $entityMgr = $this->getDoctrine()->getManager();
        $queryBuilder = $entityMgr->getRepository('DonateCoreBundle:Intent')->getQBIntentsListBy($filters);

        if ($intentForm->isValid()) {
            if ($intentForm->get('submit_export')->isClicked()) {
                $exporter = $this->get('ecollect.export.intent');
                $exporter->setExportQb($queryBuilder);
                $content = $exporter->getCsvContent();

                return $this->getCsvResponse($content, 'export_dons', 'ISO-8859-1');
            }
        }

        $pagination = $this->getPagination($request, $queryBuilder->getQuery(), 20);

        return $this->render('DonateAdminBundle:Reporting:intents.html.twig', [
            'pagination'    => $pagination,
            'intentForm'    => $intentForm->createView()
        ]);
    }

    /**
     * @Route("/intent/{id}/show" , name="donate_admin_reporting_intent_show", defaults={"id" = 0})
     */
    public function intentShowAction(Request $request, Intent $intent)
    {
        $customerId = $intent->getCustomer()->getId();
        $entityMgr = $this->getDoctrine()->getManager();

        $paymentsQuery = $entityMgr->getRepository('DonateCoreBundle:Payment')->getPaymentsListByIntent(['intentId' => $intent->getId()]);
        $otherIntentsQuery = $entityMgr->getRepository('DonateCoreBundle:Intent')->getIntentsListByCustomer(['customerId' => $customerId], 5, $intent->getId());
        $otherIntents = $otherIntentsQuery->getResult();

        $pagination = $this->getPagination($request, $paymentsQuery, 12);

        return $this->render('DonateAdminBundle:Reporting:intentShow.html.twig', [
            'intent'                => $intent,
            'pagination'            => $pagination,
            'customerOtherIntents'  => $otherIntents,
        ]);
    }

    /**
     * @Route("/customers" , name="donate_admin_reporting_customers")
     */
    public function customersAction(Request $request)
    {
        $filters = array();
        $customerForm = $this->createForm(new CustomerFiltersType(), $filters, [
            'method' => 'GET'
        ]);

        $customerForm->handleRequest($request);

        $filters = $customerForm->getData();

        $entityMgr = $this->getDoctrine()->getManager();
        $query = $entityMgr->getRepository('DonateCoreBundle:Customer')->getCustomersListBy($filters);

        if ($customerForm->isValid()) {
            if ($customerForm->get('submit_export')->isClicked()) {
                $exporter = $this->get('ecollect.export.customer');
                $exporter->setExportQuery($query);
                $content = $exporter->getCsvContent();

                return $this->getCsvResponse($content, 'export_donateurs', 'ISO-8859-1');
            }
        }

        $pagination = $this->getPagination($request, $query, 20);

        return $this->render('DonateAdminBundle:Reporting:customers.html.twig', [
            'pagination'    => $pagination,
            'customerForm'  => $customerForm->createView()
        ]);

        /*
        $customerForm = $this->createForm(new CustomerFiltersType());

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

        return $this->render('DonateAdminBundle:Reporting:customers.html.twig', [
            'pagination'    => $pagination,
            'customerForm'  => $customerForm->createView()
        ]);
        */
    }

    /**
     * @Route("/customer/{id}/show" , name="donate_admin_reporting_customer_show", defaults={"id" = 0})
     */
    public function customerShowAction(Request $request, Customer $customer)
    {
        $entityMgr = $this->getDoctrine()->getManager();
        $intentsQuery = $entityMgr->getRepository('DonateCoreBundle:Intent')->getIntentsListByCustomer(['customerId' => $customer->getId()]);

        $pagination = $this->getPagination($request, $intentsQuery, 10);

        return $this->render('DonateAdminBundle:Reporting:customerShow.html.twig', [
            'customer'          => $customer,
            'pagination'        => $pagination,
        ]);
    }

    /**
     * @Route("/customer/{id}/edit" , name="donate_admin_reporting_customer_edit", defaults={"id" = 0})
     */
    public function customerEditAction(Request $request, Customer $customer)
    {
        $editForm = $this->createForm(new CustomerType(), $customer);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entityMgr = $this->getDoctrine()->getManager();

            $entityMgr->persist($customer);
            $entityMgr->flush();
            $noticeMsg = $this->get('translator')->trans("Donator has been updated");
            $this->get('session')->getFlashBag()->set('notice', $noticeMsg);

            return $this->redirect($this->generateUrl('donate_admin_reporting_customer_show', array('id' => $customer->getId())));
        }

        return $this->render('DonateAdminBundle:Reporting:customerEdit.html.twig', [
            'editForm'  => $editForm->createView()
        ]);
    }

    /**
     */
    public function customerInfoAction(Customer $customer)
    {
        return $this->render('DonateAdminBundle:Reporting:customerInfo.html.twig', [
            'customer'  => $customer
        ]);
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
