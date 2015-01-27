<?php

namespace spec\Ecedi\Donate\PaymentBundle\PaymentMethod\Plugin;

use PhpSpec\ObjectBehavior;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;
use Doctrine\Common\Persistence\ObjectManager;

class SepaOfflinePaymentMethodSpec extends ObjectBehavior
{
    private $em;

    public function let(RegistryInterface $doctrine, RouterInterface $router, EngineInterface $templating, ObjectManager $em)
    {
        $this->setDoctrine($doctrine);
        $this->setRouter($router);
        $this->setTemplating($templating);

        $this->em = $em;
        $doctrine->getManager()->willReturn($em);
        $router->generate('donate_payment_sepa_offline_completed')->willReturn('test');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\PaymentMethodInterface');
    }

    public function it_should_not_do_pay(Intent $intent)
    {
        $this->pay($intent)->shouldBe(false);
    }

    public function it_should_handle_autorize(Intent $intent)
    {
        $intent->getStatus()->willReturn(Intent::STATUS_NEW);
        $intent->getId()->willReturn(666);

        $this->em->persist($intent)->shouldBeCalled();
        $this->em->flush()->shouldBeCalled();
        $intent->setType(Intent::TYPE_RECURING)->shouldBeCalled();

        $intent->setStatus('done')->shouldBeCalled();
        $resp = $this->autorize($intent)->shouldHaveType('Symfony\Component\HttpFoundation\Response');
    }
}
