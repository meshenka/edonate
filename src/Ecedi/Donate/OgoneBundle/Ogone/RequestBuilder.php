<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2014
 * @package eDonate
 *
 *  Le RequestBuilder est un service type "Factory"
 * qui génère des Ogone\Request à partir d'un Intent et de la configuration du bundle
 *
 */
namespace Ecedi\Donate\OgoneBundle\Ogone;

use Ecedi\Donate\CoreBundle\Entity\Intent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Ecedi\Donate\OgoneBundle\Ogone\Request as OgoneRequest;
use Ecedi\Donate\OgoneBundle\Exception\CannotSignRequestException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * @since  2.5.0 use ContainerAwareInterface and ContainerAwareTrait
 */
class RequestBuilder
{
    use ContainerAwareTrait;
    /**
     * On gènère une Ecedi\Donate\OgoneBundle\Ogone\Request à partir d'un Intent
     *
     * @param  Ecedi\Donate\CoreBundle\Entity\Intent  $intent
     * @return Ecedi\Donate\OgoneBundle\Ogone\Request
     */
    public function build(Intent $intent)
    {
        $r = new OgoneRequest();

        if ($this->container->getParameter('donate_ogone.prod')) {
            $action = 'https://secure.ogone.com/ncol/prod/orderstandard_UTF8.asp';
        } else {
            $action = 'https://secure.ogone.com/ncol/test/orderstandard_UTF8.asp';
        }

        //request configuration
        $r->setMethod('POST')
            ->setAction($action)
            ->setPspid($this->container->getParameter('donate_ogone.pspid'))
            ->setOrderId($this->orderId($intent))
            ->setOperation('SAL')
            ->setPm('CreditCard')
            ->setCn($intent->getCustomer()->getLastName().' '.$intent->getCustomer()->getFirstName())
            ->setEmail($intent->getCustomer()->getEmail())
            ->setAmount($intent->getAmount())
            ->setCurrency($intent->getCurrency())
            ->setOwnerTown($intent->getCustomer()->getAddressCity())
            ->setOwnerZip($intent->getCustomer()->getAddressZipcode())
            ->setOwnerCty($intent->getCustomer()->getAddressCountry())
            ->setOwnerAddress($intent->getCustomer()->getAddressStreet())
            ->setAcceptUrl($this->absoluteUrl('donate_front_completed'))
            ->setBackUrl($this->absoluteUrl('donate_front_home'))
            ->setCancelUrl($this->absoluteUrl('donate_front_canceled'))
            ->setDeclineUrl($this->absoluteUrl('donate_front_failed'))
            ->setLanguage($this->httpLocaleToOgoneLanguage($this->container->get('request')->getLocale()))
        ;

            //TODO Logo & Template

        $this->sha1sign($r);

        return $r;
    }

    /**
     * Génération de la langue de l'interface ogone en fonction de la locale de la Request
     * @param string le http locale value
     * @return string Le code langue ISO639-1,  un « underscore » (_), et ensuite le code pays ISO3166 Alpha-2
     *
     * Le terme locale désigne en gros la langue et le pays de l'utilisateur.
     * Cela peut être n'importe quelle chaîne de caractères que votre application va utiliser pour gérer les
     * traductions et autres différences de format (par ex. format de monnaie). Le code langue ISO639-1,
     * un « underscore » (_), et ensuite le code pays ISO3166 Alpha-2 (par ex. fr_FR pour Français/France)
     * est recommandé.
     */
    protected function httpLocaleToOgoneLanguage($httpLocale = 'fr')
    {
        if ($httpLocale === 'fr') {
            return 'fr_FR'; //Français/France
        }

        return 'en_US'; //Anglais/USA
    }

    /**
     * Génération de la signature de la Requête
     *
     * @param  Ecedi\Donate\OgoneBundle\Ogone\Request                        $r
     * @return Ecedi\Donate\OgoneBundle\Ogone\Request
     * @throws Ecedi\Donate\OgoneBundle\Exception\CannotSignRequestException
     *
     * Tous les Params du formulaire sont à indiquer en MAJUSCULE dans la clé sha et
     * à ordoner dans l'ordre alphabétique
     *
     */
    protected function sha1sign(OgoneRequest $r)
    {
        $sha1inkey =  $this->container->getParameter('donate_ogone.security.sha1_in');
        $hashKey =  '';

        $shaParams = $r->jsonSerialize(); //on recupère toutes les valeurs de la Ogone\Request
        ksort($shaParams);

        foreach ($shaParams as $key => $val) {
            if ($val != '') {
                $hashKey .= $key.'='.$val.$sha1inkey;
            }
        }

        $logger = $this->container->get('logger');

        $logger->info("clef à hasher $hashKey");
        $logger->info('hash '.hash('sha1', $hashKey));

        return $r->setSha1in(hash('sha1', $hashKey));
    }

    /**
     * Génère un numéro de commande Ogone à partir de l'IntentId
     * @param  Ecedi\Donate\CoreBundle\Entity\Intent $intent
     * @return string
     */
    protected function orderId(Intent $intent)
    {
        return $this->container->getParameter('donate_ogone.prefix').'-'.$intent->getId();
    }

    /**
     * Génération d'une URL absolue
     * @param  string $route      le nom de la route
     * @param  array  $parameters parametres de la route
     * @return string url absolue
     */
    protected function absoluteUrl($route, $parameters = array())
    {
        $httpRequest = $this->container->get('request');

        return $this->container->get('router')->generate(
                $route,
                ['_locale' => $httpRequest->getLocale()],
                UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
