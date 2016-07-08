<?php
/**
 * @author  Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi 2014
 */
namespace Ecedi\Donate\CoreBundle\IntentManager;

use Ecedi\Donate\CoreBundle\Entity\Intent;

/**
 * Cette interface est l'API de référence pour la gestion de l'Intent Manager
 * Son objectif est de traiter.
 *
 * @api
 *
 * @since 1.0.0
 */
interface IntentManagerInterface
{
    /**
     * Handle any incoming donation intent, it should delegate real business logic to
     * the appropriate Payment Method.
     *
     * @param Intent $intent An incoming donation intent
     *
     * @return Symfony\Component\HttpFoundation\Response an HTTP Response object that will be returned by a Controller that Manage Intent
     *
     * @since  2.0.0
     */
    public function handle(Intent $intent);
}
