<?php

namespace Ecedi\Donate\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Ecedi\Donate\CoreBundle\Entity\Customer;
use Ecedi\Donate\CoreBundle\Form\CustomerType;

/**
* @NamePrefix("donate_api_v1_")
*
* L' annotation ...View(serializerGroups={"REST"}) utilisée ci-dessous permet de retourner
* seulement les éléments de l'entité qui appartiennent au groupe "REST" (défini dans l'entité)
* cf: Customer Entity et l'annotation ...Groups({"REST"})
*/
class CustomersController extends Controller
{
    /**
    * @View(serializerGroups={"REST"})
    * @param ParamFetcher $paramFetcher
    * @return array
    */
    public function getCustomersAction(ParamFetcher $paramFetcher)
    {
        $restParams = $paramFetcher->All();   // On récupère tous les paramètres passés en GET

        $em = $this->getDoctrine()->getManager();
        $customerRepo = $em->getRepository('DonateCoreBundle:Customer');

        $customers = $customerRepo->findByRestParams($restParams);
        $nbResults = $customerRepo->countAll();

        return [
            'nbResults' => $nbResults,
            'customers' => $customers
        ];
    }

    /**
    * @View(serializerGroups={"REST"})
    * @param int $customerId
    * @return Customer
    */
    public function getCustomerAction($customerId)
    {
        $em = $this->getDoctrine()->getManager();
        $customer = $em->getRepository('DonateCoreBundle:Customer')->find($customerId);

        $this->throwNotFoundExceptionIfNotCustomer($customer);  // Contrôle sur l'existence de l'entité

        return [
            'customer' => $customer
        ];
    }

    /**
    * @get("customer/by-email")
    * @QueryParam(name="email", description="Customer email")
    * @View(serializerGroups={"REST"})
    * @param ParamFetcher $paramFetcher
    * @return Customer
    */
    public function getCustomerByEmailAction(ParamFetcher $paramFetcher)
    {
        $email = $paramFetcher->get('email');

        $em = $this->getDoctrine()->getManager();
        $customer = $em->getRepository('DonateCoreBundle:Customer')->findOneByEmail(['email' => $email]);

        $this->throwNotFoundExceptionIfNotCustomer($customer);  // Contrôle sur l'existence de l'entité

        return [
            'customer' => $customer
        ];
    }

    /**
    * @get("customer/by-remote-id")
    * @QueryParam(name="remote_id", description="Customer remote Id")
    * @View(serializerGroups={"REST"})
    * @param ParamFetcher $paramFetcher
    * @return Customer
    */
    public function getCustomerByRemoteIdAction(ParamFetcher $paramFetcher)
    {
        $remoteId = $paramFetcher->get('remote_id');

        $em = $this->getDoctrine()->getManager();
        $customer = $em->getRepository('DonateCoreBundle:Customer')->findOneByRemoteId(['remoteId' => $remoteId]);

        $this->throwNotFoundExceptionIfNotCustomer($customer);  // Contrôle sur l'existence de l'entité

        return [
            'customer' => $customer
        ];
    }

    /**
     * @View(statusCode=204) -- "No content" - Retourné quand l'update de l'entité a été réalisé
     * @param int $customerId
     */
    public function patchCustomerAction($customerId)
    {
        $em = $this->getDoctrine()->getManager();
        $customer = $em->getRepository('DonateCoreBundle:Customer')->find($customerId);

        $this->throwNotFoundExceptionIfNotCustomer($customer);  // Contrôle sur l'existence de l'entité

        return $this->processForm($customer, 'PATCH');
    }

    /**
    * Processing the Customer form
    *
    * @param Customer $customer
    * @param string $method -- la méthode du formulaire pour récupérer les données
    */
    private function processForm(Customer $customer, $method = 'POST')
    {
        $form = $this->createForm(new CustomerType(), $customer, array('method' => $method));
        $form->handleRequest($this->getRequest());

        if ($form->isValid()) { // Si les données sont correctes, on enregistre notre Customer
            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush();
        }
    }

    /**
    * Envoie d'une Exception (404 -- Not Found) si l'on ne retrouve pas le Customer
    *
    * @param $customer
    */
    private function throwNotFoundExceptionIfNotCustomer($customer)
    {
        if (!$customer instanceof Customer) {
            throw $this->createNotFoundException("Customer not found, check id or parameters.");
        }
    }
}
