<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @package Ecollecte
 */
namespace Ecedi\Donate\OgoneBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Ecedi\Donate\OgoneBundle\Ogone\Response;

/**
 * Ogone Post Sale Hook event
 *
 * @since  2.2.0
 */
class PostSaleEvent extends Event
{
    /**
     *
     * @var Response
     */
    private $response;

   /**
    * intent
    *
    * @return Intent intent
    */
   public function getResponse()
   {
       return $this->response;
   }

    public function __construct(Response $response)
    {
        $this->response = $response;
    }
}
