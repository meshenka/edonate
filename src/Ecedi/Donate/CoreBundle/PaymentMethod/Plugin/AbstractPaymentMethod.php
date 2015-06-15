<?php
/**
 * @author  Sylvain Gogel <sgogel@ecedi.fr>
 * @package eDonate
 * @subpackage PaymentMethod
 */

namespace Ecedi\Donate\CoreBundle\PaymentMethod\Plugin;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Routing\RouterInterface;
use Ecedi\Donate\CoreBundle\Entity\Intent;

/**
 * This class is a helper that wire some default Symfony2 dependencies.
 * It will be used in services.xml to define parent service class and avoid repetitive
 * dependencies injection
 *
 * @since  1.2.0
 * @see  http://symfony.com/fr/doc/current/components/dependency_injection/parentservices.html
 * @example
 * <services>
 *
 *   <service id="ecollect.payment_method" abstract="true">
 *       <call method="setTemplating">
 *            <argument type="service" id="templating" />
 *       </call>
 *       <call method="setDoctrine">
 *            <argument type="service" id="doctrine" />
 *       </call>
 *       <call method="setRouter">
 *            <argument type="service" id="router" />
 *       </call>
 *   </service>
 *   <service id="donate_ogone.payment_method" class="%donate_ogone.payment_method.class%" parent="ecollect.payment_method">
 *       <tag name="donate.payment_method" />
 *   </service>
 * </services>
 *
 */
abstract class AbstractPaymentMethod implements PaymentMethodInterface
{
    /**
     * Template Engine
     * @var Symfony\Component\Templating\EngineInterface
     */
    protected $templating;

    /**
     * Persistence
     * @var Symfony\Bridge\Doctrine\RegistryInterface
     */
    protected $doctrine;

    /**
     * Router
     * @var Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    public function setTemplating(EngineInterface $templating)
    {
        $this->templating = $templating;

        return $this;
    }

    public function getTemplating()
    {
        return $this->templating;
    }

    public function setDoctrine(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;

        return $this;
    }

    public function getDoctrine()
    {
        return $this->doctrine;
    }

    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;

        return $this;
    }

    public function getRouter()
    {
        return $this->router;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getId();

    /**
     * {@inheritdoc}
     */
    abstract public function getName();

    /**
     * {@inheritdoc}
     */
    abstract public function getTunnel();

    /**
     * {@inheritdoc}
     */
    abstract public function autorize(Intent $intent);

    /**
     * {@inheritdoc}
     */
    abstract public function pay(Intent $intent);
}
