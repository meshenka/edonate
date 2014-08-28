<?php

namespace Ecedi\Donate\ApiBundle\Controller;

//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Controller\FOSRestController;
// use FOS\RestBundle\Controller\Annotations\RouteResource;
// use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\NamePrefix;

/**
 * @NamePrefix("donate_api_v1_")
 * RouteResource("v1")
 */
class DefaultController extends FOSRestController
{
    /**
     */
    public function getDonatorsAction()
    {
        return array('name' => "");
    }

    public function getDonatorAction($donatorId)
    {
        return array('name' => $donatorId);
    }

}
