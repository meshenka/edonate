<?php
/**
 * @author  Sylvain Gogel <sgogel@ecedi.fr>
 * @package UnitTest
 * @subpackage PaymentMethod
 * @copyright Agence Ecedi 2014
 */
namespace spec\Ecedi\Donate\OgoneBundle\Service;

use PhpSpec\ObjectBehavior;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;
use Ecedi\Donate\CoreBundle\Entity\Intent;

/**
 * Ogone PaymentMethod Spec
 * @since  2.0.0
 */
class OgonePaymentMethodSpec extends ObjectBehavior
{
    public function let(RegistryInterface $doctrine, RouterInterface $router, EngineInterface $templating)
    {
        $this->setDoctrine($doctrine);
        $this->setRouter($router);
        $this->setTemplating($templating);

        $router->generate('donate_ogone_pay')->willReturn('test');
    }
    public function it_is_initializable()
    {
        $this->shouldHaveType('Ecedi\Donate\OgoneBundle\Service\OgonePaymentMethod');
        $this->shouldHaveType('Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\PaymentMethodInterface');
    }

    public function it_should_return_a_response(Intent $intent)
    {
        $intent->setStatus(Intent::STATUS_PENDING)->willReturn($intent);
        $intent->getStatus()->willReturn(Intent::STATUS_NEW);
        $intent->getId()->willReturn(666);

        $resp = $this->pay($intent)->shouldHaveType('Symfony\Component\HttpFoundation\Response');
    }
}
